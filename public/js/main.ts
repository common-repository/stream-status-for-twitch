'use strict';

import '../scss/entry.scss';

import { getStreams } from './utils/getStreams';

interface StreamWeaselsOptions {
	uuid: string;
	twitchUsername?: string;
	kickUsername?: string;
	youtubeUsername?: string;
	nonce: string;
}

function initStreamWeasels(element: HTMLElement) {
	const opts: StreamWeaselsOptions = {
		uuid: element.dataset.uuid || '',
		twitchUsername: element.dataset.twitchUsername,
		kickUsername: element.dataset.kickUsername,
		youtubeUsername: element.dataset.youtubeUsername,
		nonce: element.dataset.nonce || '',
	};

	getStreams(
		opts.twitchUsername,
		opts.kickUsername,
		opts.youtubeUsername,
		opts.nonce,
		element
	);
}

document.addEventListener('DOMContentLoaded', () => {
	const elements = document.querySelectorAll('.cp-sw-status-bar');
	elements.forEach((element) => {
		initStreamWeasels(element as HTMLElement);
	});
});
