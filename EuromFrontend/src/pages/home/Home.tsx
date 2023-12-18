import React from 'react';
import { useTokenData } from '@/lib/useTokenData';
import Menu from '@/components/Menu';

export default function Home() {
	const { token } = useTokenData();
	if (token)
		return (
			<>
				<Menu />
				{JSON.stringify(token)}
			</>
		);
	return <></>;
}
