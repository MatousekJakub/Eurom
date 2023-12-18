import { UseMutationOptions, UseMutationResult } from '@tanstack/react-query';
import React, { ReactElement, useEffect, useState } from 'react';
import {
	Button,
	Form as FormBS,
	FormCheck,
	FormControl,
	FormSelect,
	InputGroup,
} from 'react-bootstrap';
import Spinner from './Spinner';
import * as Icons from 'react-bootstrap-icons';
import Modal from './Modal';
import ModalSelector from '../ModalSelector';

export default function Form(props: React.ComponentProps<typeof FormBS>) {
	return <FormBS {...props}>{props.children}</FormBS>;
}

function SimpleInput(
	props: React.ComponentProps<typeof FormControl> & {
		inputRef?: React.Ref<HTMLInputElement>;
	},
) {
	const { inputRef, ...propsWithoutInputRef } = props;
	return <FormControl {...propsWithoutInputRef} ref={inputRef} className='' />;
}

Form.DataInput = function DataInput({
	id,
	editId,
	...props
}: React.ComponentProps<typeof FormControl> & {
	id: string;
	className?: string;
	inputRef: React.Ref<HTMLInputElement>;
	label?: string;
	value: string | number | string[] | undefined;
	editId: number | undefined;
	mutator: (
		id: number,
		options?: UseMutationOptions<any, any, any, any>,
	) => UseMutationResult<any, any, any, any>;
}) {
	const { value, mutator, className, inputRef, ...filteredProps } = props;
	const [statusState, setStatusState] = useState<
		'loading' | 'error' | 'idle' | 'success'
	>('idle');
	const { mutateAsync: mutate } = mutator(editId || 0);
	if (!!editId)
		return (
			<FormBS.Group className={className}>
				{!!props.label && (
					<FormBS.Label htmlFor={props.id}>{props.label}</FormBS.Label>
				)}
				<InputGroup>
					<FormControl
						{...filteredProps}
						className=''
						id={id}
						value={value}
						onChange={(e) => {
							console.log({ [id]: e.target.value });
							setStatusState('loading');
							mutate(
								{ [id]: e.target.value },
								{
									onSettled(data, error) {
										if (data) setStatusState('success');
										if (error) setStatusState('error');
									},
								},
							);
						}}
					/>
					<Button variant={'primary'} disabled>
						{(statusState === 'idle' || statusState === 'success') && (
							<Icons.CheckLg />
						)}
						{statusState === 'loading' && <Spinner size='sm' />}
						{statusState === 'error' && <Icons.XLg />}
						{/* {statusState ? <Spinner size='sm' /> : <Icons.CheckLg />} */}
					</Button>
				</InputGroup>
			</FormBS.Group>
		);
	return (
		<Form.Input
			{...filteredProps}
			className={className}
			id={id}
			inputRef={inputRef}
			key={`${id}uncontrolled`}
		/>
	);
};

Form.Input = function Input(
	props: React.ComponentProps<typeof FormControl> & {
		label?: string;
		description?: string;
		id: string;
		inputRef?: React.Ref<HTMLInputElement>;
		unit?: string | ReactElement;
		hintlist?: string[];
	} & (
			| {
					button: string | ReactElement;
					onButtonClick: React.MouseEventHandler<HTMLButtonElement>;
					buttonVariant?: 'primary' | 'secondary' | 'success' | 'danger';
			  }
			| {
					button?: undefined;
					onButtonClick?: undefined;
					buttonVariant?: undefined;
			  }
		),
) {
	const { onButtonClick, inputRef, ...filteredProps } = props;
	return (
		<FormBS.Group>
			{!!props.label && (
				<FormBS.Label htmlFor={props.id}>{props.label}</FormBS.Label>
			)}
			{(props.unit || props.button) && (
				<InputGroup>
					{props.unit && <InputGroup.Text>{props.unit}</InputGroup.Text>}
					<FormControl
						list={`list${props.id}`}
						{...filteredProps}
						ref={inputRef}
						className=''
					/>
					{props.button && (
						<Button
							variant={props.buttonVariant ?? 'primary'}
							onClick={onButtonClick}
						>
							{props.button}
						</Button>
					)}
				</InputGroup>
			)}

			{!props.unit && !props.button && (
				<FormControl
					list={`list${props.id}`}
					{...filteredProps}
					ref={inputRef}
				/>
			)}
			{props.hintlist && (
				<datalist id={`list${props.id}`}>
					{props.hintlist.map((hint) => (
						<option key={hint} value={hint} />
					))}
				</datalist>
			)}
			{!!props.description && <FormBS.Text>{props.description}</FormBS.Text>}
		</FormBS.Group>
	);
};
Form.Check = function Check(
	props: React.ComponentProps<typeof FormCheck> & {
		heading?: string;
		id: string;
		inputRef?: React.Ref<HTMLInputElement>;
	},
) {
	const { inputRef, ...propsWithoutInputRef } = props;
	return (
		<FormBS.Group controlId={props.id}>
			{!!props.heading && <FormBS.Label>{props.heading}</FormBS.Label>}
			<FormBS.Check {...propsWithoutInputRef} ref={inputRef} />
		</FormBS.Group>
	);
};
Form.DataCheck = function DataCheck(
	props: React.ComponentProps<typeof FormCheck> & {
		heading?: string;
		id: string;
		inputRef: React.Ref<HTMLInputElement>;
		label?: string;
		checked?: boolean;
		editId: number | undefined;
		mutator: (
			id: number,
			options?: UseMutationOptions<any, any, any, any>,
		) => UseMutationResult<any, any, any, any>;
		className: string;
	},
) {
	const { inputRef, editId, checked, className, mutator, ...filteredProps } =
		props;
	const [statusState, setStatusState] = useState<
		'loading' | 'error' | 'idle' | 'success'
	>('idle');
	const { mutateAsync: mutate } = mutator(editId || 0);
	if (!!editId)
		return (
			<FormBS.Group controlId={props.id}>
				{!!props.heading && <FormBS.Label>{props.heading}</FormBS.Label>}
				<InputGroup>
					<FormBS.Check
						{...filteredProps}
						checked={checked}
						onChange={(e) => {
							setStatusState('loading');
							mutate(
								{ [props.id]: e.target.checked },
								{
									onSettled(data, error) {
										if (data) setStatusState('success');
										if (error) setStatusState('error');
									},
								},
							);
						}}
					/>
					<Button variant={'primary'} disabled>
						{(statusState === 'idle' || statusState === 'success') && (
							<Icons.CheckLg />
						)}
						{statusState === 'loading' && <Spinner size='sm' />}
						{statusState === 'error' && <Icons.XLg />}
						{/* {statusState ? <Spinner size='sm' /> : <Icons.CheckLg />} */}
					</Button>
				</InputGroup>
			</FormBS.Group>
		);
	return (
		<FormBS.Check
			{...filteredProps}
			className={className}
			id={props.id}
			ref={inputRef}
			key={`${props.id}uncontrolled`}
		/>
	);
};
Form.Select = function Select(
	props: React.ComponentProps<typeof FormSelect> & {
		label?: string;
		description?: string;
		id: string;
		inputRef?: React.Ref<HTMLSelectElement>;
	},
) {
	const { inputRef, ...propsWithoutInputRef } = props;
	return (
		<FormBS.Group>
			<FormBS.Label htmlFor={props.id}>{props.label}</FormBS.Label>
			<FormBS.Select {...propsWithoutInputRef} ref={inputRef}>
				{props.children}
			</FormBS.Select>
		</FormBS.Group>
	);
};
Form.InputWithSearch = function InputWithSearch(
	props: (React.ComponentProps<typeof FormControl> & {
		label?: string;
		description?: string;
		id: string;
		inputRef?: React.Ref<HTMLInputElement>;
		unit?: string | ReactElement;
	}) &
		(
			| {
					button: string | ReactElement;
					onButtonClick: React.MouseEventHandler<HTMLButtonElement>;
					buttonVariant?: 'primary' | 'secondary' | 'success' | 'danger';
			  }
			| {
					button?: undefined;
					onButtonClick?: undefined;
					buttonVariant?: undefined;
			  }
		),
) {
	const { inputRef, ...propsWithoutInputRef } = props;
	const {
		button,
		onButtonClick,
		buttonVariant,
		unit,
		id,
		label,
		...propsWithoutButton
	} = propsWithoutInputRef;
	const { children, ...propsWithoutChildren } = propsWithoutButton;
	return (
		<FormBS.Group>
			{<FormBS.Label htmlFor={id}>{label}</FormBS.Label>}
			{(!!unit || !!button) && (
				<InputGroup>
					{unit && <InputGroup.Text>{unit}</InputGroup.Text>}
					<SimpleInput
						list={`${id}list`}
						{...propsWithoutChildren}
						ref={inputRef}
						className=''
					/>
					{button && (
						<Button
							variant={buttonVariant ?? 'primary'}
							onClick={onButtonClick}
						>
							{button}
						</Button>
					)}
				</InputGroup>
			)}

			{!props.unit && !props.button && (
				<FormBS.Control
					list={`${props.id}list`}
					{...propsWithoutChildren}
					ref={inputRef}
				/>
			)}

			<datalist id={`${props.id}list`}>{children}</datalist>
		</FormBS.Group>
	);
};
Form.Textarea = function Textarea(
	props: React.ComponentProps<typeof FormControl> & {
		label?: string;
		description?: string;
		id: string;
		inputRef?: React.Ref<HTMLTextAreaElement>;
	},
) {
	const { inputRef, ...propsWithoutInputRef } = props;
	return (
		<FormBS.Group>
			{!!props.label && (
				<FormBS.Label htmlFor={props.id}>{props.label}</FormBS.Label>
			)}
			<FormControl {...propsWithoutInputRef} ref={inputRef} as='textarea' />
			{!!props.description && <FormBS.Text>{props.description}</FormBS.Text>}
		</FormBS.Group>
	);
};
Form.Separator = function Separator() {
	return <hr />;
};
Form.SelectWithSearch = function SelectWithSearch<T extends { id: number }>(
	props: React.ComponentProps<typeof FormControl> & {
		label?: string;
		description?: string;
		id: string;
		outputIdSetter: (id: number) => void;
		unit?: string | ReactElement;
		searchlist: T[];
		searchlistkey: keyof T;
		initialValue?: string;
	} & (
			| {
					button: string | ReactElement;
					onButtonClick: React.MouseEventHandler<HTMLButtonElement>;
					buttonVariant?: 'primary' | 'secondary' | 'success' | 'danger';
			  }
			| {
					button?: undefined;
					onButtonClick?: undefined;
					buttonVariant?: undefined;
			  }
		),
) {
	const {
		onButtonClick,
		inputRef,
		outputIdSetter,
		initialValue,
		...filteredProps
	} = props;
	const [value, setValue] = useState('');
	const [isShown, setIsShown] = useState(false);

	const [searchString, setSearchString] = useState('');
	const filteredList = props.searchlist.filter((item) =>
		(item[props.searchlistkey] as string)
			.toString()
			.toLowerCase()
			.includes(searchString.toLowerCase()),
	);
	useEffect(() => setValue(initialValue || ''), [initialValue]);
	if (!props.searchlist) return <Spinner size='sm' />;
	return (
		<FormBS.Group>
			{!!props.label && (
				<FormBS.Label htmlFor={props.id}>{props.label}</FormBS.Label>
			)}
			{(props.unit || props.button) && (
				<InputGroup>
					{props.unit && <InputGroup.Text>{props.unit}</InputGroup.Text>}
					<FormControl
						readOnly
						onClick={() => setIsShown(true)}
						value={value}
						list={`list${props.id}`}
						{...filteredProps}
						className=''
					/>
					{props.button && (
						<Button
							variant={props.buttonVariant ?? 'primary'}
							onClick={onButtonClick}
						>
							{props.button}
						</Button>
					)}
				</InputGroup>
			)}
			{!props.unit && !props.button && (
				<FormControl
					readOnly
					onClick={() => setIsShown(true)}
					value={value}
					list={`list${props.id}`}
					{...filteredProps}
				/>
			)}
			{!!props.description && <FormBS.Text>{props.description}</FormBS.Text>}
			<ModalSelector
				show={isShown}
				onHide={() => setIsShown(false)}
				searchlist={props.searchlist}
				searchlistkey={props.searchlistkey}
				callback={(id: number) => {
					setValue(
						(props.searchlist.find((item) => item.id === id)?.[
							props.searchlistkey
						] as string) || '',
					);
					outputIdSetter(id);
					setIsShown(false);
				}}
			/>
			{/* <Modal show={isShown} onHide={() => setIsShown(false)}>
				<Modal.Header closeButton>Zvolte ze seznamu</Modal.Header>
				<Modal.Body>
					<FormBS.Control
						className='mb-3'
						value={searchString}
						onChange={(e) => setSearchString(e.target.value)}
						placeholder='Zadejte hledaný výraz'
					/>
					{filteredList.map((item) => (
						<Button
							className='m-1'
							key={item.id}
							onClick={() => {
								setValue(item[props.searchlistkey] as string);
								outputIdSetter(item.id);
								setIsShown(false);
							}}
						>
							{item.id} - {item[props.searchlistkey] as string}
						</Button>
					))}
				</Modal.Body>
			</Modal> */}
		</FormBS.Group>
	);
};
