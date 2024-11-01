export async function getTwitchStream(
	twitchUsername: string,
    siteUrl: string,
	nonce: string
): Promise<{
	streamData: { liveStatus: string; game: string; viewers: string };
}> {
	// Default streamData
	let streamData = {
		liveStatus: 'offline',
		game: 'N/A',
		viewers: '0',
	};

	try {
		const response = await fetch(
			siteUrl+`/?rest_route=/swsb/v1/fetch-streams/&user_login=${twitchUsername}`,
			{
				headers: { 'X-WP-Nonce': nonce },
			}
		);

		if (!response.ok) {
			throw new Error(
				`Failed to fetch stream data: HTTP status ${response.status}`
			);
		}

		const data = await response.json();

		if (data.data && data.data[0]?.type === 'live') {
			streamData = {
				liveStatus: data.data[0].type,
				game: data.data[0].game_name,
				viewers: data.data[0].viewer_count,
			};
		}

		return { streamData };
	} catch (error) {
		console.error('Error fetching stream data:', error);
		return { streamData };
	}
}
