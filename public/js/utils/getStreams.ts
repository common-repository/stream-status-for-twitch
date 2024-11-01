import { appendData } from './appendData';
import { getYouTubeStream } from './getYouTubeStream';
import { getTwitchStream } from './getTwitchStream';
import { getKickStream } from './getKickStream';

export async function getStreams(
	twitchUsername: string | undefined,
	kickUsername: string | undefined,
	youtubeUsername: string | undefined,
	nonce: string,
	element: HTMLElement
) {

  const siteUrl = element.dataset.siteUrl ?? '';

	if (twitchUsername) {
		const twitchData = await getTwitchStream(twitchUsername, siteUrl, nonce);
		appendData(element, twitchData.streamData, 'twitch');
	}
	if (kickUsername) {
		const kickData = await getKickStream(kickUsername, nonce);
		appendData(element, kickData.streamData, 'kick');
	}
	if (youtubeUsername) {
		const youTubeData = await getYouTubeStream(youtubeUsername, siteUrl, nonce);
		appendData(element, youTubeData.streamData, 'youtube');
	}
}
