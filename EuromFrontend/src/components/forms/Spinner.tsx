import React from 'react';
import { Spinner as SpinnerBS } from 'react-bootstrap';

export default function Spinner(props: React.ComponentProps<typeof SpinnerBS>) {
	return <SpinnerBS style={{ margin: '0 auto' }} {...props} />;
}
