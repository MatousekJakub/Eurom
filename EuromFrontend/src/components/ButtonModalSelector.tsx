import React, { useState } from 'react';
import { Button } from 'react-bootstrap';
import ModalSelector from './ModalSelector';

export default function ButtonModalSelector<T extends { id: number }>({
	searchlist,
	searchlistkey,
	callback,
	children,
	...props
}: {
	searchlist: T[];
	searchlistkey: keyof T;
	callback: (id: number) => void;
	children: React.ReactNode;
} & React.ComponentProps<typeof Button>) {
	const [show, setShow] = useState(false);
	return (
		<>
			<Button {...props} onClick={() => setShow(true)}>
				{children}
			</Button>
			<ModalSelector
				show={show}
				onHide={() => setShow(false)}
				searchlist={searchlist}
				searchlistkey={searchlistkey}
				callback={(id) => {
					setShow(false);
					callback(id);
				}}
			/>
		</>
	);
}
