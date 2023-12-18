import { toast as callToast } from 'react-toastify';
export default function toast(
	message: string,
	options: Parameters<typeof callToast>[1] = {}
) {
	callToast(message, {
		position: callToast.POSITION.BOTTOM_RIGHT,
		theme: 'colored',
		type: 'success',
		autoClose: 1000,
		...options,
	});
}
toast.promise = (
	fn: Parameters<typeof callToast.promise>[0],
	messages: Parameters<typeof callToast.promise>[1],
	options: Parameters<typeof callToast.promise>[2] = {}
) => {
	return callToast.promise(fn, messages, { theme: 'colored', ...options });
};
