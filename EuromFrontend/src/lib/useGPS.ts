import { useState } from 'react';

type GPS =
	| { location: GeolocationPosition; error: null }
	| { location: null; error: 'denied' | 'unavailable' | 'timeout' };

export default function useGPS() {
	const [location, setLocation] = useState<GeolocationPosition | null>(null);
	const [error, setError] = useState<
		'denied' | 'unavailable' | 'timeout' | null
	>(null);
	const [watchId, setWatchId] = useState<number | null>(null);
	if (watchId == null)
		setWatchId(
			navigator.geolocation.watchPosition(
				(position) => {
					setError(null);
					setLocation(position);
				},
				(error) => {
					setLocation(null);
					console.log('position error');
					switch (error.code) {
						case error.PERMISSION_DENIED:
							if (watchId) navigator.geolocation.clearWatch(watchId);
							break;
						case error.POSITION_UNAVAILABLE:
							setError('unavailable');
							break;
						case error.TIMEOUT:
							setError('timeout');
							break;
					}
				},
				{
					enableHighAccuracy: true,
					timeout: 5000,
					maximumAge: 0,
				}
			)
		);
	if (location && error === null) return { location, error } as GPS;
	if (location === null) return { location, error } as GPS;
	if (error === 'denied' && location === null)
		return { location, error } as GPS;
	if (error === 'unavailable' && location === null)
		return { location, error } as GPS;
	if (error === 'timeout' && location === null)
		return { location, error } as GPS;
	return { location, error } as GPS;
}
