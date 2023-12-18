<?php

namespace Eurom\Generation;

use Eurom\Orm\DBHandler;
use PDO;


class Generator
{
    private $dbStructure;
    private $MySQLTypeMap;
    private $TSTypeMap;
    private $modelPath = "app/model/";
    private $routePath = "app/routes/";
    private $TSPath = "exports/ts/";
    function __construct()
    {

        $this->MySQLTypeMap["text"] = "string";
        $this->MySQLTypeMap["datetime"] = "string";
        $this->MySQLTypeMap["timestamp"] = "string";
        $this->MySQLTypeMap["int"] = "int";
        $this->MySQLTypeMap["float"] = "float";
        $this->MySQLTypeMap["double"] = "float";
        $this->MySQLTypeMap["tinyint(1)"] = "bool";

        $this->TSTypeMap["text"] = "string";
        $this->TSTypeMap["datetime"] = "string";
        $this->TSTypeMap["timestamp"] = "string";
        $this->TSTypeMap["int"] = "number";
        $this->TSTypeMap["float"] = "number";
        $this->TSTypeMap["double"] = "number";
        $this->TSTypeMap["tinyint(1)"] = "boolean";

        $db = DBHandler::create();
        $schema = array();
        $tablesQuery = $db->query("SHOW TABLES");
        $tables = $tablesQuery->fetchAll(PDO::FETCH_NUM);
        foreach ($tables as $tab) {
            $table = $tab[0];
            $columnsQuery = $db->query("DESCRIBE " . $table);
            $columns = $columnsQuery->fetchAll();
            $schema[$table] = $columns;
        }
        $structure = [];
        foreach ($schema as $tableName => $fields) {
            foreach ($fields as $field) {
                $fieldType = $field["Type"];
                if ($field["Field"] === "id") continue;
                $structure[$tableName][$field["Field"]]["type"] = $this->MySQLTypeMap[$fieldType];
                $structure[$tableName][$field["Field"]]["TStype"] = $this->TSTypeMap[$fieldType];
                $structure[$tableName][$field["Field"]]["default"] = $field["Default"];
                $structure[$tableName][$field["Field"]]["nullable"] = $field["Null"] === "YES" ? true : false;
            }
        }
        $this->dbStructure = $structure;
    }
    private function generateRoutes($forced)
    {
        foreach ($this->dbStructure as $tableName => $columns) {
            if ($tableName === "login") continue;
            $directory = str_replace("generator.php", "", $_SERVER['SCRIPT_FILENAME']) . $this->routePath . $tableName;
            $dbObjectName = ucfirst($tableName);
            if (!is_dir($directory))
                mkdir($directory, 0777, true);

            $defaultRoute =
                '<?php

namespace Routes\\' . $dbObjectName . ';

use Eurom\\Routing\\BaseController;
use Eurom\\Routing\\RouteAttributes\\RequiredLogin;
use Model\\' . $dbObjectName . 'Factory;
use Slim\\Psr7\\Request;
use Slim\\Psr7\\Response;

#[RequiredLogin(1)]
class Controller extends BaseController
{
    //Return all ' . $dbObjectName . 's
    function get(Request $request, Response $response, $args)
    {
        $' . $dbObjectName . ' = ' . $dbObjectName . 'Factory::getAll();
        $response->getBody()->write(json_encode($' . $dbObjectName . '));
        return $response;
    }

    //Create a ' . $dbObjectName . '
    function post(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        if (!$id = ' . $dbObjectName . 'Factory::create($body)) return $response->withStatus(400);
        $' . $dbObjectName . ' = ' . $dbObjectName . 'Factory::getById($id);
        $response->getBody()->write(json_encode($' . $dbObjectName . '));
        return $response->withStatus(201);
    }
}
';
            $idRoute =
                '<?php

namespace Routes\\' . $dbObjectName . '\\Id;

use Eurom\\Routing\\BaseController;
use Eurom\\Routing\\RouteAttributes\\RequiredArgs;
use Eurom\\Routing\\RouteAttributes\\RequiredLogin;
use Model\\' . $dbObjectName . 'Factory;
use Slim\\Psr7\\Request;
use Slim\\Psr7\\Response;

#[RequiredArgs("id"), RequiredLogin(1)]
class Controller extends BaseController
{
    //Return a ' . $dbObjectName . ' with id
    function get(Request $request, Response $response, $args)
    {
        if (!' . $dbObjectName . 'Factory::exists($args["id"])) return $response->withStatus(404);
        $' . $dbObjectName . ' = ' . $dbObjectName . 'Factory::getById($args["id"]);
        $response->getBody()->write(json_encode($' . $dbObjectName . '));
        return $response;
    }
    //Edit a ' . $dbObjectName . ' with id
    function post(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $id = $args["id"];
        if (!' . $dbObjectName . 'Factory::exists($id)) return $response->withStatus(404);
        $' . $dbObjectName . ' = ' . $dbObjectName . 'Factory::getById($id);
        if (!$' . $dbObjectName . '->setAll($body))
            return $response->withStatus(400);
        $response->getBody()->write(json_encode($' . $dbObjectName . '));
        return $response->withStatus(200);
    }
    function delete(Request $request, Response $response, $args)
    {
        $id = $args["id"];
        if (!' . $dbObjectName . 'Factory::exists($id)) return $response->withStatus(404);
        $' . $dbObjectName . ' = ' . $dbObjectName . 'Factory::getById($id);
        if (!$' . $dbObjectName . '->delete())
            return $response->withStatus(400);
        return $response->withStatus(204);
    }
}
';
            $this->writeFile($directory . "/default.php", $defaultRoute, $forced);
            $this->writeFile($directory . "/{id}.php", $idRoute, $forced);
        }
    }
    public function generateDBObjects($forced)
    {
        foreach ($this->dbStructure as $table => $columns) {
            if ($table === "login") continue;
            $this->generateDBObject($table, $columns, $forced);
        }
    }
    private function generateDBObject(string $name, $props, $forced)
    {
        $dbObject = $this->generateHeading();
        $dbObject .= $this->generateClassHeading($name);
        $dbObject .= $this->generateProps($props);
        $dbObject .= $this->generateFactory($name);
        $path = str_replace("generator.php", "", $_SERVER['SCRIPT_FILENAME']) . $this->modelPath . ucfirst($name) . ".php";
        $this->writeFile($path, $dbObject, $forced);
        return $dbObject;
    }
    private function generateHeading(): string
    {
        return
            '<?php

namespace Model;

use Eurom\Orm\BaseFactory;
use Eurom\Orm\DBObject;

';
    }
    private function generateClassHeading(string $columnName): string
    {
        return
            'class ' . ucfirst($columnName) . ' extends DBObject
{
';
    }
    private function generateProps($columns): string
    {
        $out = "";
        foreach ($columns as $columnName => $columnProps) {
            $end = "";
            if ($columnProps["default"] !== null) {
                $end = $columnProps["type"] === "bool" ? ($columnProps["default"] === 1 ? "true" : "false") : $columnProps["default"];
                if ($columnProps["type"] === "string") {
                    $end = "\"" . $end . "\"";
                }
                $end = " = " . $end;
            }
            $end = $end . ";\r\n";
            $out .= "    public " . ($columnProps["nullable"] ? "?" : null) . $columnProps["type"] . " $" . $columnName . $end;
        }
        $out .= "}\r\n";
        return $out;
    }
    private function generateFactory($tableName): string
    {
        return
            'class ' . ucfirst($tableName) . 'Factory extends BaseFactory
{
    static function getById(int $id): ' . ucfirst($tableName) . ' | false
    {
        return parent::getById($id);
    }
    /**
     * @return ' . ucfirst($tableName) . '[]
     */
    static function getAll(): array
    {
        return parent::getAll();
    }
    /**
     * @return ' . ucfirst($tableName) . '[]
     */
    static function getAllWhere(string | array $target, $value = null): array
    {
        return parent::getAllWhere($target, $value);
    }
}
';
    }
    private function writeFile(string $path, string $body, bool $forced)
    {
        if (!$forced && file_exists($path))
            return;
        $file = fopen($path, 'w');
        fwrite($file, $body);
        fclose($file);
    }
    public function generateAll(bool $forced)
    {
        $this->generateDBObjects($forced);
        $this->generateRoutes($forced);
        $this->generateTS($forced);
    }
    function generateTS($forced)
    {
        $directory = str_replace("generator.php", "", $_SERVER['SCRIPT_FILENAME']) . $this->TSPath . "/model/";
        if (!is_dir($directory))
            mkdir($directory, 0777, true);
        foreach ($this->dbStructure as $tableName => $columns) {
            if ($tableName === "login") continue;
            $dbObjectName = ucfirst($tableName);
            if (!is_dir($directory . "/" . lcfirst($dbObjectName) . "/"))
                mkdir($directory . "/" . lcfirst($dbObjectName) . "/", 0777, true);
            $out = "import { useFetch } from '@/lib/useFetch';\r\n";
            $out .= "import { useModel } from '@/lib/useModel';\r\n";
            $out .= "import { UseQueryOptions } from '@tanstack/react-query';\r\n\r\n";
            $out .= "export type " . $dbObjectName . "DTO = {
";
            $out .= "	id: number;\r\n";
            foreach ($columns as $propName => $propDetails) {
                $out .= "	" . $propName;
                if ($propDetails["nullable"]) $out .= "?";
                $out .= ": " . $propDetails["TStype"] . ";\r\n";
            }
            $out .= "}\r\n";
            $out .= "export type New" . $dbObjectName . "DTO = Omit<" . $dbObjectName . "DTO, 'id'>;\r\n";
            $out .= "export const useModel" . $dbObjectName . "s = useModel<" . $dbObjectName . "DTO[]>({
    queryKey: ['" . lcfirst($dbObjectName) . "s'],
    queryFn: () => useFetch('/" . lcfirst($dbObjectName) . "'),
});

export const useModel" . $dbObjectName . " = (
    id?: number,
    options?: UseQueryOptions<" . $dbObjectName . "DTO> & { once?: boolean }
) =>
    useModel<" . $dbObjectName . "DTO>({
        enabled: !!id,
        queryKey: ['" . lcfirst($dbObjectName) . "s', { id: id }],
        queryFn: () => useFetch('/" . lcfirst($dbObjectName) . "/' + id),
        ...options,
    })(options);
";
            $this->writeFile($directory . "/" . $dbObjectName . "/useModel" . $dbObjectName . ".ts", $out, $forced);
            if (!is_dir($directory . "/" . lcfirst($dbObjectName) . "/mutations"))
                mkdir($directory . "/" . lcfirst($dbObjectName) . "/mutations", 0777, true);
            $outCreate = "import { useFetch } from '@/lib/useFetch';
import { UseMutationOptions, useQueryClient } from '@tanstack/react-query';
import { useModelMutation } from '@/lib/useModelMutation';
import { " . $dbObjectName . "DTO, New" . $dbObjectName . "DTO } from '../useModel" . $dbObjectName . "';\r\n\r\n";
            $outCreate .= "type TData = " . $dbObjectName . "DTO;
type TError = any;
type TVariables = New" . $dbObjectName . "DTO;
type TContext = " . $dbObjectName . "DTO[];\r\n
export const use" . $dbObjectName . "Create = (
    options?: UseMutationOptions<TData, TError, TVariables, TContext>
) => {
    const queryClient = useQueryClient();
    return useModelMutation({
        mutationFn: (body) => useFetch('/" . lcfirst($dbObjectName) . "', 'POST', body),
        onMutate: (new" . $dbObjectName . ") => {
            queryClient.cancelQueries({ queryKey: ['" . lcfirst($dbObjectName) . "s'] });
            const previous" . $dbObjectName . "s = queryClient.getQueryData(['" . lcfirst($dbObjectName) . "s']);
            queryClient.setQueryData<" . $dbObjectName . "DTO[] | New" . $dbObjectName . "DTO[]>(
                ['" . lcfirst($dbObjectName) . "s'],
                (old) => [...(old || []), new" . $dbObjectName . "]
            );
            return previous" . $dbObjectName . "s as " . $dbObjectName . "DTO[];
        },
        onError: (err, oldId, previous" . $dbObjectName . "s) => {
            queryClient.setQueryData(['" . lcfirst($dbObjectName) . "s'], previous" . $dbObjectName . "s);
        },
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['" . lcfirst($dbObjectName) . "s'] });
        },
        ...options,
    })();
};";
            $this->writeFile($directory . "/" . $dbObjectName . "/mutations/use" . $dbObjectName . "Create.ts", $outCreate, $forced);
            $outEdit = "import { useFetch } from '@/lib/useFetch';
import { UseMutationOptions, useQueryClient } from '@tanstack/react-query';
import { useModelMutation } from '@/lib/useModelMutation';
import { " . $dbObjectName . "DTO } from '../useModel" . $dbObjectName . "';

type TData = " . $dbObjectName . "DTO;
type TError = any;
type TVariables = Partial<" . $dbObjectName . "DTO>;
type TContext = " . $dbObjectName . "DTO[];

export const use" . $dbObjectName . "Edit = (
    id: number,
    options?: UseMutationOptions<TData, TError, TVariables, TContext>
) => {
    const queryClient = useQueryClient();
    return useModelMutation({
        mutationFn: (body) => useFetch(`/" . lcfirst($dbObjectName) . "/\${id}`, 'POST', body),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['" . lcfirst($dbObjectName) . "s'] });
        },
        onError: (err, oldId, previous" . $dbObjectName . "s) => {
            queryClient.setQueryData(['" . lcfirst($dbObjectName) . "s'], previous" . $dbObjectName . "s);
        },
        onMutate: (new" . $dbObjectName . ") => {
            queryClient.cancelQueries({ queryKey: ['" . lcfirst($dbObjectName) . "s'] });
            const previous" . $dbObjectName . "s = queryClient.getQueryData(['" . lcfirst($dbObjectName) . "s']);
            queryClient.setQueryData<" . $dbObjectName . "DTO[]>(['" . lcfirst($dbObjectName) . "s'], (old) =>
                old?.map((" . lcfirst($dbObjectName) . ") =>
                    " . lcfirst($dbObjectName) . ".id === id ? { ..." . lcfirst($dbObjectName) . ", ...new" . $dbObjectName . " } : " . lcfirst($dbObjectName) . "
                )
            );
            return previous" . $dbObjectName . "s as " . $dbObjectName . "DTO[];
        },
        ...options,
    })();
};";
            $this->writeFile($directory . "/" . $dbObjectName . "/mutations/use" . $dbObjectName . "Edit.ts", $outEdit, $forced);
            $outDelete = "import { useFetch } from '@/lib/useFetch';
import { UseMutationOptions, useQueryClient } from '@tanstack/react-query';
import { useModelMutation } from '@/lib/useModelMutation';
import { " . $dbObjectName . "DTO } from '../useModel" . $dbObjectName . "';

type TData = string;
type TError = any;
type TVariables = number;
type TContext = " . $dbObjectName . "DTO[];

export const use" . $dbObjectName . "Delete = (
    options?: UseMutationOptions<TData, TError, TVariables, TContext>
) => {
    const queryClient = useQueryClient();
    return useModelMutation<TData, TError, TVariables, TContext>({
        mutationFn: (id: number) => useFetch(`/" . lcfirst($dbObjectName) . "/\${id}`, 'DELETE'),
        onSettled: () => {
            queryClient.invalidateQueries({ queryKey: ['" . lcfirst($dbObjectName) . "s'] });
        },
        onMutate: (oldId) => {
            queryClient.cancelQueries({ queryKey: ['" . lcfirst($dbObjectName) . "s'] });
            const previous" . $dbObjectName . "s = queryClient.getQueryData(['" . lcfirst($dbObjectName) . "s']);
            queryClient.setQueryData<" . $dbObjectName . "DTO[]>(['" . lcfirst($dbObjectName) . "s'], (old) =>
                old?.filter((" . lcfirst($dbObjectName) . ") => " . lcfirst($dbObjectName) . ".id !== oldId)
            );
            return previous" . $dbObjectName . "s as " . $dbObjectName . "DTO[];
        },
        onError: (error, variables, previous" . $dbObjectName . "s) => {
            queryClient.setQueryData(['" . lcfirst($dbObjectName) . "s'], previous" . $dbObjectName . "s);
        },
        ...options,
    })();
};
            ";
            $this->writeFile($directory . "/" . $dbObjectName . "/mutations/use" . $dbObjectName . "Delete.ts", $outDelete, $forced);
        }
    }
}
