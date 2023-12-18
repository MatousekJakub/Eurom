export function useTextUtils() {
	const getTextColorByBackground = (background: string | undefined): string => {
		if (background === undefined) return 'black';
		const bg = background.substring(1);
		const r = bg.slice(0, 2);
		const g = bg.slice(2, 4);
		const b = bg.slice(4, 6);
		const brightness = Math.round(
			(parseInt(r, 16) * 299 + parseInt(g, 16) * 587 + parseInt(b, 16) * 114) /
				1000
		);
		return brightness > 125 ? 'black' : 'white';
	};
	const getTimeZoneAdjustedTime = (time: Date) => {
		return new Date(
			new Date(
				new Date(time).getTime() - time.getTimezoneOffset() * 60 * 1000
			).getTime()
		)
			.toISOString()
			.slice(0, 19)
			.replace('T', ' ');
	};
	return { getTextColorByBackground, getTimeZoneAdjustedTime };
}
