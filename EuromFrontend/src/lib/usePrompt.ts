import { useState } from 'react';
import { IPrompt } from '../components/Prompt';

export const usePrompt = <TParams>(
	text: string,
	callback: (params: TParams) => void
) => {
	const [prompt, setPrompt] = useState<IPrompt>({
		text: text,
		callback: callback,
		handleClose: handleClose,
		isShown: false,
		data: null,
	});
	function handleClose() {
		setPrompt({ ...prompt, isShown: false });
	}
	function showPrompt(data: TParams) {
		setPrompt({ ...prompt, isShown: true, data: data });
	}
	return {
		prompt: showPrompt,
		promptData: prompt,
		setPrompt: setPrompt,
		close: handleClose,
	};
};
