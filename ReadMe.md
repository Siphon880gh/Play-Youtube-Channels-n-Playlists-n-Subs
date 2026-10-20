# Youtube Shuffler
By Weng Fei Fung

<a target="_blank" href="https://github.com/Siphon880gh" rel="nofollow"><img src="https://img.shields.io/badge/GitHub--blue?style=social&logo=GitHub" alt="Github" data-canonical-src="https://img.shields.io/badge/GitHub--blue?style=social&logo=GitHub" style="max-width:100%;"></a>
<a target="_blank" href="https://www.linkedin.com/in/weng-fung/" rel="nofollow"><img src="https://img.shields.io/badge/LinkedIn-blue?style=flat&logo=linkedin&labelColor=blue" alt="Linked-In" data-canonical-src="https://img.shields.io/badge/LinkedIn-blue?style=flat&amp;logo=linkedin&amp;labelColor=blue" style="max-width:100%;"></a>
<a target="_blank" href="https://www.youtube.com/@WayneTeachesCode/" rel="nofollow"><img src="https://img.shields.io/badge/Youtube-red?style=flat&logo=youtube&labelColor=red" alt="Youtube" data-canonical-src="https://img.shields.io/badge/Youtube-red?style=flat&amp;logo=youtube&amp;labelColor=red" style="max-width:100%;"></a>  

## Basics

Add playlists to a Youtube shuffler or queuer. You can shuffle or play the videos from the most recent to the oldest. There are other features as well.

You can add playlists at /favs. Each .json file is a collection. The Default.json is the collection that shows up when you load the shuffler. 

You can list the collections of playlists by clicking "Youtube uploads and playlists" three times.

## Optional: YouTube API features (upload date + playlist titles)

A YouTube Data API key is optional. Without a key (or with an invalid/restricted key), the shuffler still works as usual:

- The current video's **upload date** stays hidden.
- The playlist list (click the `N / total` control next to Mode) still opens and you can jump to videos, but rows show placeholders like `Video 1` instead of real titles.

With a valid key, you get:

- Upload date under the Mode dropdown while a video is playing.
- Real titles in the playlist list.

To enable these:

1. Create a project in [Google Cloud Console](https://console.cloud.google.com/).
2. Open **APIs & Services → Library**, search for **YouTube Data API v3**, and enable it.
3. Open **APIs & Services → Credentials → Create credentials → API key**.
4. (Recommended) Restrict the key to **YouTube Data API v3**, and for HTTP referrers restrict it to your site (for example `https://your-domain.example/*` and `http://localhost/*` while testing).
5. Copy `.env.example` to `.env` in this app folder.
6. Put your API key in `.env`:

```env
# Optional: upload date + playlist video titles in the player UI.
YOUTUBE_API_KEY=YOUR_API_KEY_HERE
```

`.env` is gitignored. After that, reload the shuffler.

### YouTube Data API v3 quota (free)

Yes, the YouTube Data API v3 is free to use. You get a daily limit of **10,000 quota units** per project without requiring a credit card. The quota resets at midnight Pacific Time.

| Request type | Typical cost | Rough daily room under 10,000 |
|---|---|---|
| Read requests (video details, channel stats) | 1 unit per call | ~10,000 reads/day |
| Search requests | 100 units per call | ~100 searches/day |
| Write requests (e.g. upload a video) | 1,600 units | limited |

This app only uses **read** calls (`videos.list`):

- **Upload date:** 1 unit per current-video lookup. Cached in the browser under `localStorage` keyed by channel name + video title (up to 2,000 entries), so revisiting the same video later does not spend another unit.
- **Playlist titles:** 1 unit per batch of up to 50 video IDs when you open the playlist list. Titles are cached in memory for the session.

If you go over 10,000 units, the API returns a **403** until the next daily reset. In that case the shuffler still works; the upload date stays hidden and playlist rows fall back to `Video N` placeholders until quota is available again.

## Channel Uploads as a Playlist

A channel's entire uploads can be added as a playlist. Their channel's Home tab may have a uploads row where you can play all. If not, then at the Videos tab, see if you can click "Play all" under Uploads -> All Videos. The URL will have &list= that gives you the playlist ID you want to add to a .json file.

Finally, some channels don't have "Play all" from both Home and Video tabs, so you can't get the uploads playlist normally. Open channel's home view source. Then look for `vnd`. Around vnd is the channel ID which starts with `UC`. Copy that code in the URL. Instead of starting with UC, change those two characters to `UU`. Copy the UU string to url parameter `&list=` on any of the channel's video. Then reload. It'll load the playlist. The UU is your uploads playlist ID you'll be adding to a .json file. This info on getting the uploads playlist from channels that don't have "Play All" is from https://webapps.stackexchange.com/questions/9702/is-there-a-way-to-play-all-youtube-videos-uploaded-by-a-particular-user.

You could open in VS Code and regular expression search with:
```
vnd.*?UC
```

## Collection entries

Each file in `favs/` is a JSON array of objects. An object has one of these two shapes.

A playlist plays in the video player:

```json
{ "id": "UUxxxx", "name": "[Category] Channel or playlist title" }
```

`id` is a YouTube playlist id. `name` is the label in the list. The first `[brackets]` in `name` become the section header.

A guide opens a page and shows instructions. Paste the page you want into `open`, and the words for the modal into `instructions`:

```json
{
  "name": "[Films/Recaps/Trailers/Movies] Rotten Tomatoes Current Month (Manual)",
  "open": "https://www.youtube.com/@RottenTomatoesTRAILERS/playlists",
  "instructions": "Click the playlist for the current month. Copy its URL, then paste it into Playlist ID."
}
```

Clicking a guide opens `open` in a new tab and shows `instructions` on this app. It has no playlist id, so nothing is added to the JSON file from that click.

Playlist ID, in the Playlist controls, accepts a playlist id, a playlist URL, or a channel URL. A channel URL plays that channel’s uploads playlist. Run loads it in the video player for this visit and leaves the JSON file unchanged.

An object with only `name` is a divider line.

A shelf row has `group` and no playlist id. The categories after it belong to that shelf until the next shelf row. All topics shows those shelves.

```json
{ "group": "Mood" }
```

A section row has `section` and no playlist id. It starts a category for entries that have no `[brackets]`.

```json
{ "section": "NLP" }
```