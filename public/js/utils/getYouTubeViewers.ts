import { getYouTubeCategory } from './getYouTubeCategory';

export async function getYouTubeViewers(
	videoId: string,
    siteUrl: string,
	nonce: string
): Promise<{ game: string; viewers: string }> {
	try {
		const response = await fetch(
			siteUrl+`/?rest_route=/swsb/v1/fetch-youtube-viewers/&id=${videoId}`,
			{
				headers: { 'X-WP-Nonce': nonce },
			}
		);

		if (!response.ok) {
			throw new Error(
				`Failed to fetch extra data: HTTP status ${response.status}`
			);
		}

		const data = await response.json();

		if (data.items && data.items[0]?.snippet?.categoryId) {
			const game = await getYouTubeCategory(
				data.items[0]?.snippet?.categoryId,
                siteUrl,
				nonce
			);
			return {
				game,
				viewers: data.items[0]?.liveStreamingDetails?.concurrentViewers,
			};
		} else {
			throw new Error('Failed to fetch extra data: Invalid data format');
		}
	} catch (error: any) {
		throw new Error(`Failed to fetch extra data: ${error.message}`);
	}
}
