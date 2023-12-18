import { UseMutationOptions, useMutation } from '@tanstack/react-query';

export type QueryOptions = UseMutationOptions;

export const useModelMutation = <
	TData = unknown,
	TError = unknown,
	TVariables = void,
	TContext = unknown
>(
	defaultOptions: UseMutationOptions<TData, TError, TVariables, TContext>
) => {
	return (options?: UseMutationOptions<TData, TError, TVariables, TContext>) =>
		useMutation({ ...defaultOptions, ...options });
};
