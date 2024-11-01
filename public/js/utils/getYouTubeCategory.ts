export async function getYouTubeCategory(
	categoryId: string,
    siteUrl: string,
	nonce: string
): Promise<string> {
	try {
		const response = await fetch(
			siteUrl+`/?rest_route=/swsb/v1/fetch-youtube-category/&id=${categoryId}`,
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

		if (data && data.items[0]?.snippet?.title) {
			return data.items[0]?.snippet?.title;
		} else {
			throw new Error('Failed to fetch extra data: Invalid data format');
		}
	} catch (error: any) {
		throw new Error(`Failed to fetch extra data: ${error.message}`);
	}
}
