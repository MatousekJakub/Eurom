import { QueryKey, UseQueryOptions, useQuery } from '@tanstack/react-query';

export const useModel = <
	TQueryFnData,
	TError = unknown,
	TData = TQueryFnData,
	TQueryKey extends QueryKey = QueryKey
>(
	defaultOptions: UseQueryOptions<TQueryFnData, TError, TData, TQueryKey>
) => {
	const fetchOnceParams = {
		refetchInterval: Infinity,
		refetchOnMount: false,
		refetchOnReconnect: false,
		refetchOnWindowFocus: false,
	};
	return (
		options?: UseQueryOptions<TQueryFnData, TError, TData, TQueryKey> & {
			once?: boolean;
		}
	) => {
		if (options?.once)
			defaultOptions = { ...defaultOptions, ...fetchOnceParams };
		return useQuery<TQueryFnData, TError, TData, TQueryKey>({
			...defaultOptions,
			...options,
		});
	};
};
