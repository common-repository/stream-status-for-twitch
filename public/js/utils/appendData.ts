export function appendData(
	element: HTMLElement,
	streamData: {
		liveStatus: string | undefined;
		game: string | undefined;
		viewers: string | undefined;
	},
	platform: 'twitch' | 'youtube' | 'kick'
) {
	// Decrement the loading state with each appendData call
	const loadingState = parseInt(element.dataset.barLoading ?? '0');
	element.dataset.barLoading = (loadingState - 1).toString();

	// Set the liveStatus attribute if it's not already live
	if (element.dataset.liveStatus !== 'live') {
		element.dataset.liveStatus = streamData.liveStatus ?? 'offline';
	}

	if (streamData.liveStatus === 'live') {
		var currentViewerCheck =
			element.querySelector('.cp-sw-status-bar__viewers--line-2')!
				.textContent ?? '0';
		var currentViewerCheclkInt = parseInt(currentViewerCheck);
		var newViewerCheckInt = parseInt(streamData.viewers ?? '0');

		element.classList.add(
			'l-stream-online',
			'l-stream-online--' + platform
		);
		element
			.querySelector('.cp-sw-status-bar__indicator')!
			.classList.add('cp-sw-status-bar__indicator--online');
		(element.querySelector(
			`.l-${platform}-cta`
		) as HTMLElement)!.dataset.status = 'live';
		(element.querySelector(
			`.l-${platform}-cta`
		) as HTMLElement)!.dataset.viewers = streamData.viewers ?? '';

		// Only change the game and viewers if the new viewer count is higher than the current viewer count
		if (newViewerCheckInt > currentViewerCheclkInt) {
			element.querySelector(
				'.cp-sw-status-bar__game--line-2'
			)!.textContent = streamData.game ?? '';
			element.querySelector(
				'.cp-sw-status-bar__viewers--line-2'
			)!.textContent = streamData.viewers ?? '';
		}
	} else {
		(element.querySelector(
			`.l-${platform}-cta`
		) as HTMLElement)!.dataset.status = 'offline';
	}
}
