# Youtube Shuffler
By Weng Fei Fung

<a target="_blank" href="https://github.com/Siphon880gh" rel="nofollow"><img src="https://img.shields.io/badge/GitHub--blue?style=social&logo=GitHub" alt="Github" data-canonical-src="https://img.shields.io/badge/GitHub--blue?style=social&logo=GitHub" style="max-width:100%;"></a>
<a target="_blank" href="https://www.linkedin.com/in/weng-fung/" rel="nofollow"><img src="https://camo.githubusercontent.com/0f56393c2fe76a2cd803ead7e5508f916eb5f1e62358226112e98f7e933301d7/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f4c696e6b6564496e2d626c75653f7374796c653d666c6174266c6f676f3d6c696e6b6564696e266c6162656c436f6c6f723d626c7565" alt="Linked-In" data-canonical-src="https://img.shields.io/badge/LinkedIn-blue?style=flat&amp;logo=linkedin&amp;labelColor=blue" style="max-width:100%;"></a>
<a target="_blank" href="https://www.youtube.com/user/Siphon880yt/" rel="nofollow"><img src="https://camo.githubusercontent.com/0bf5ba8ac9f286f95b2a2e86aee46371e0ac03d38b64ee2b78b9b1490df38458/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f596f75747562652d7265643f7374796c653d666c6174266c6f676f3d796f7574756265266c6162656c436f6c6f723d726564" alt="Youtube" data-canonical-src="https://img.shields.io/badge/Youtube-red?style=flat&amp;logo=youtube&amp;labelColor=red" style="max-width:100%;"></a>  

## Basics

Add playlists to a Youtube shuffler or queuer. You can shuffle or play the videos from the most recent to the oldest. There are other features as well.

You can add playlists at /favs. Each .json file is a collection. The Default.json is the collection that shows up when you load the shuffler. 

You can list the collections of playlists by clicking "Youtube uploads and playlists" three times.

## Channel Uploads as a Playlist

A channel's entire uploads can be added as a playlist. Their channel's Home tab may have a uploads row where you can play all. If not, then at the Videos tab, see if you can click "Play all" under Uploads -> All Videos. The URL will have &list= that gives you the playlist ID you want to add to a .json file.

Finally, some channels don't have "Play all" from both Home and Video tabs, so you can't get the uploads playlist normally. Open channel's home view source. Then look for vnd. Around vnd is the channel ID which starts with UC. Copy that code. Instead of starting with UC, change those two characters to UU. Copy the UU string to url parameter "&list=" on any of the channel's video. Then reload. It'll load the playlist. The UU is your uploads playlist ID you'll be adding to a .json file. This info on getting the uploads playlist from channels that don't have "Play All" is from https://webapps.stackexchange.com/questions/9702/is-there-a-way-to-play-all-youtube-videos-uploaded-by-a-particular-user.