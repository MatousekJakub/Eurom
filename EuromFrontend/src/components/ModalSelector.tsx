import React, { useState } from 'react';
import Modal from './forms/Modal';
import { Button, Form } from 'react-bootstrap';

export default function ModalSelector<T extends { id: number }>({
	show,
	onHide,
	searchlist,
	searchlistkey,
	callback,
}: {
	show: boolean;
	onHide: () => void;
	searchlist: T[];
	searchlistkey: keyof T;
	callback: (id: number) => void;
}) {
	const [searchString, setSearchString] = useState('');
	const filteredList = searchlist.filter((item) =>
		(item[searchlistkey] as string)
			.toString()
			.toLowerCase()
			.includes(searchString.toLowerCase()),
	);
	return (
		<Modal show={show} onHide={onHide}>
			<Modal.Header closeButton>Zvolte ze seznamu</Modal.Header>
			<Modal.Body>
				<Form.Control
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
							callback(item.id);
						}}
					>
						{item.id} - {item[searchlistkey] as string}
					</Button>
				))}
			</Modal.Body>
		</Modal>
	);
}
