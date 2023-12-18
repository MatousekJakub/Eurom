<?php

namespace Eurom\Files;

use Slim\Psr7\Response;

class FileReader
{
    static function writeFileToResponse($fileName, Response $response)
    {

        if (!file_exists($fileName)) return $response->withStatus(404);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $contentType = finfo_file($finfo, $fileName);
        finfo_close($finfo);
        $response = $response->withHeader('Content-Type', $contentType);
        $response = $response->withHeader('Content-Disposition', "filename=" . $fileName);
        $response = $response->withHeader('Content-Length', filesize($fileName));
        $response = $response->withHeader('Expires', '0');
        $response = $response->withHeader('Cache-Control', 'must-revalidate');

        $response->getBody()->write(file_get_contents($fileName));

        return $response;
    }
}
