import { getYouTubeViewers } from './getYouTubeViewers';

export async function getYouTubeStream(
	youtubeUsername: string,
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
			siteUrl+`/?rest_route=/swsb/v1/fetch-youtube/&user_login=${youtubeUsername}`,
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

		if (
			data.items &&
			data.items[0]?.snippet?.liveBroadcastContent === 'live'
		) {
			const extraData = await getYouTubeViewers(
				data.items[0]?.id?.videoId,
                siteUrl,
				nonce
			);
			streamData = {
				liveStatus: data.items[0]?.snippet?.liveBroadcastContent,
				game: extraData.game,
				viewers: extraData.viewers,
			};
		}

		return { streamData };
	} catch (error) {
		console.error('Error fetching stream data:', error);
		// Return default streamData in case of an error
		return { streamData };
	}
}
