export async function getKickStream(
	kickUsername: string,
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
			`https://kick.com/api/v1/channels/${kickUsername}`,
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

		if (data && data.livestream) {
			streamData = {
				liveStatus: data.livestream.is_live ? 'live' : 'offline',
				game: data.livestream.categories[0].category.name,
				viewers: data.livestream.viewer_count,
			};
		}

		return { streamData };
	} catch (error) {
		console.error('Error fetching stream data:', error);
		return { streamData };
	}
}
