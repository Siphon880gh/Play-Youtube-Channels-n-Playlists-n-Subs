# Youtube Shuffler
By Weng Fei Fung

<a target="_blank" href="https://github.com/Siphon880gh" rel="nofollow"><img src="https://img.shields.io/badge/GitHub--blue?style=social&logo=GitHub" alt="Github" data-canonical-src="https://img.shields.io/badge/GitHub--blue?style=social&logo=GitHub" style="max-width:100%;"></a>
<a target="_blank" href="https://www.linkedin.com/in/weng-fung/" rel="nofollow"><img src="https://img.shields.io/badge/LinkedIn-blue?style=flat&logo=linkedin&labelColor=blue" alt="Linked-In" data-canonical-src="https://img.shields.io/badge/LinkedIn-blue?style=flat&amp;logo=linkedin&amp;labelColor=blue" style="max-width:100%;"></a>
<a target="_blank" href="https://www.youtube.com/@WayneTeachesCode/" rel="nofollow"><img src="https://img.shields.io/badge/Youtube-red?style=flat&logo=youtube&labelColor=red" alt="Youtube" data-canonical-src="https://img.shields.io/badge/Youtube-red?style=flat&amp;logo=youtube&amp;labelColor=red" style="max-width:100%;"></a>  

## Basics

Add playlists to a Youtube shuffler or queuer. You can shuffle or play the videos from the most recent to the oldest. There are other features as well.

You can add playlists at /favs. Each .json file is a collection. The Default.json is the collection that shows up when you load the shuffler. 

You can list the collections of playlists by clicking "Youtube uploads and playlists" three times.

## Optional: Video upload date

Showing the current video's upload date is optional. Without a YouTube Data API key, the app works as usual and the upload date is simply hidden.

To enable it:

1. Create a project in [Google Cloud Console](https://console.cloud.google.com/).
2. Open **APIs & Services → Library**, search for **YouTube Data API v3**, and enable it.
3. Open **APIs & Services → Credentials → Create credentials → API key**.
4. (Recommended) Restrict the key to **YouTube Data API v3**, and for HTTP referrers restrict it to your site (for example `https://your-domain.example/*` and `http://localhost/*` while testing).
5. Copy `.env.example` to `.env` in this app folder.
6. Put your API key in `.env`:

```env
# Optional: adds the current video's upload date in the player UI.
YOUTUBE_API_KEY=YOUR_API_KEY_HERE
```

`.env` is gitignored. After that, reload the shuffler; the upload date appears under the Mode dropdown when a video is playing.

### YouTube Data API v3 quota (free)

Yes, the YouTube Data API v3 is free to use. You get a daily limit of **10,000 quota units** per project without requiring a credit card. The quota resets at midnight Pacific Time.

| Request type | Typical cost | Rough daily room under 10,000 |
|---|---|---|
| Read requests (video details, channel stats) | 1 unit per call | ~10,000 reads/day |
| Search requests | 100 units per call | ~100 searches/day |
| Write requests (e.g. upload a video) | 1,600 units | limited |

This app only uses a **read** call (`videos.list`) for the current video's upload date, so each date lookup costs **1 unit**. Results are cached in the browser for the session, so switching back to a video you already loaded does not spend another unit.

If you go over 10,000 units, the API returns a **403** until the next daily reset. In that case the shuffler still works; the upload date is simply hidden until quota is available again.

## Channel Uploads as a Playlist

A channel's entire uploads can be added as a playlist. Their channel's Home tab may have a uploads row where you can play all. If not, then at the Videos tab, see if you can click "Play all" under Uploads -> All Videos. The URL will have &list= that gives you the playlist ID you want to add to a .json file.

Finally, some channels don't have "Play all" from both Home and Video tabs, so you can't get the uploads playlist normally. Open channel's home view source. Then look for `vnd`. Around vnd is the channel ID which starts with `UC`. Copy that code in the URL. Instead of starting with UC, change those two characters to `UU`. Copy the UU string to url parameter `&list=` on any of the channel's video. Then reload. It'll load the playlist. The UU is your uploads playlist ID you'll be adding to a .json file. This info on getting the uploads playlist from channels that don't have "Play All" is from https://webapps.stackexchange.com/questions/9702/is-there-a-way-to-play-all-youtube-videos-uploaded-by-a-particular-user.

You could open in VS Code and regular expression search with:
```
vnd.*?UC
```