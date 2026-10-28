---
name: adding-youtube-playlists
description: >-
  Use when the user gives YouTube channel links, video links, or playlist
  links, or links from another social or video platform, to add, file, sort,
  or categorize in this playlist app, including turning a channel or video
  into its latest-uploads playlist, turning a post into that account's
  profile, and choosing a playlist icon.
---

# Adding YouTube playlists

Channel and video links become that channel's uploads playlist (latest videos). A link that is already a playlist stays that playlist. A social or video link becomes that account's profile or channel URL, not the single post. Each result is inserted into the matching category in `favs/*.json` unless that category already has the same playlist id or profile URL. Every added row needs an icon rule in `env/playlist-icons.js`.

The resolver does not edit those files:

```bash
node .agents/skills/adding-youtube-playlists/scripts/resolve-youtube-links.js URL [URL...]
```

## Resolve

Run the resolver on every link before choosing a category.

| Link | Result |
|---|---|
| `/playlist?list=ID`, or `list=` with no video id | Keep that playlist id |
| `/watch?v=`, `youtu.be/`, `/shorts/`, `/embed/`, `/live/` | Uploads playlist of the video's channel, even when the URL also has `list=` |
| `/channel/UC...`, `/@handle`, `/c/...`, `/user/...`, `/Name` | Uploads playlist of that channel |
| Instagram, TikTok, X, Facebook, Threads, Twitch, Vimeo, Rumble, Reddit | That account's profile or channel URL |

An uploads playlist id is the channel id with the leading `UC` replaced by `UU`. That playlist is the channel's videos from newest to oldest. Do not call `search.list`. Do not invent an id when the resolver returns `error`; skip that link and report it.

`resolution: "playlist link"` means the id was kept. `resolution: "uploads playlist"` means it was derived from the channel. `resolution: "profile link"` means the URL is the account profile or channel. A profile result has no playlist id.

## Profiles

A profile row has no `id`. Do not set `open` on it. `open` is only a guide.

The resolver returns `url`, `href`, `title`, `label`, and `platform`. `href` is the protocol-relative profile URL to store. `label` is the suffix, including the parentheses. Instagram is `(Instagram)`. TikTok is `(Tiktok)`.

A post, reel, short, or video keeps the profile or channel and drops the post. A username already in the path is enough. A short link, or a post with no username, is fetched. If the profile cannot be found, the resolver returns `error`; skip that link and report it. Do not save the post URL.

Use `title` as the display name. If `title` is only the handle because the page gave no display name, you may use a display name that the same handle shows on a public page. Do not invent one.

```text
[Category] Display name (Instagram)
```

The suffix is the resolver's `label`. The anchor is required even when the neighboring rows have no HTML.

## Category

Read `favs/*.json` in this run. Do not rely on a remembered category list.

A collection is one JSON file. `Default.json` is the collection the shuffler loads. A category is the first `[bracket]` in an entry name after HTML tags are removed. The app turns that bracket into the section header. A row with no `id` and no `open` is a separator, not a playlist. A row `{ "group": "Shelf" }` starts a shelf. Later rows stay in that shelf until the next group row, and All topics groups categories under that shelf. `Default.json` shelves are Mood, Practice, Comedy, Crimes, Lifestyle, Curiosity, Films, Videogames, Videogame Playthroughs, Opinion, Reviews, Travel, One-offs, Cars, Fitness, Self, and Skills. Put a new category in the shelf where that kind of channel already sits. A row `{ "section": "Label" }` starts a category for rows that have no `[brackets]`. A row with `open` and `instructions` is a guide: clicking it opens that URL and shows the instructions, and it is not a playlist id. The first row of a collection may be `{ "theme": "youtube-red" }`, `"matrix-green"`, `"monochrome"` or `"velvet"`; it only sets the colors, so keep it first and add new rows after it. A row `{ "shortcuts": [ ... ] }`, right after the theme row, holds that collection's quick-filter links; it is not a playlist or a separator, so leave it alone when adding playlists and add new rows after it. Game playthroughs go in the Videogame Playthroughs shelf. Their names have no `[brackets]`, and the shelf starts with `{ "section": "Videogame Playthroughs" }`.

1. Use the collection and category the user named. Copy an existing category string exactly, including capitals and slashes.
2. Otherwise pick the existing category whose label fits the channel or playlist, in the collection where that category already appears.
3. If that category exists in more than one collection, use the collection whose neighboring rows are the same kind of channel.
4. Use the Videogame Playthroughs shelf for a game playthrough, and match that shelf's unbracketed names.
5. Add a new `[Category]` only when no existing category fits. Put it in `Default.json` unless the user named another collection. Do not create a new collection file unless the user asks.

Display name, matching the neighbors in that category:

```text
[Category] Channel or playlist title
```

Use the resolver's `title` (the channel name, the playlist title when the link was already a playlist, or the profile display name). Keep a neighbor's `(Shuffle)`, HTML, or emoji only when that category already uses it. A profile row still gets its anchor and platform suffix.

## Duplicates

The resolver's `existing` array lists every saved copy of that playlist id or profile URL.

Drop the link when the chosen collection already has that id, or that profile URL, in any category. Tell the user which entry was already there. Profile comparison ignores `www`, the protocol, and a trailing slash.

Two input links that resolve to the same id or the same profile URL are one add.

## Icon

Read the header of `env/playlist-icons.js`. Rules run in order. The first matching pattern wins. If `pattern` and `reject` both match, that rule does not apply and later rules are not tried.

The app tests a rule against both the playlist name and the section label (the text inside the first brackets, without the brackets). HTML in the name is ignored. For a profile, pass the plain name, with the anchor removed.

```bash
node .agents/skills/adding-youtube-playlists/scripts/resolve-youtube-links.js --icon-check "SECTION" "[Category] Name"
```

If both labels already match a rule whose icon fits, leave `playlist-icons.js` unchanged.

If either label does not match, add one rule to `playlistIconRules`. Default pattern, which matches the section exactly and the same text inside brackets:

```bash
node .agents/skills/adding-youtube-playlists/scripts/resolve-youtube-links.js --pattern "Category"
```

Use a broader regular expression only when one icon should cover several labels that belong together. Extend an existing rule when its icon is already the right one. Put a specific rule before a broader rule that would also match. Insert the object before the `];` that closes `playlistIconRules`.

Set one of `emoji`, `font`, `svg`, or `src`:

- `emoji`: one emoji that depicts the topic
- `font`: a Font Awesome 5 free class loaded by `env/icon-fonts.php`, such as `"fas fa-utensils"`
- `svg`: a small inline SVG that uses `currentColor`
- `src`: a local image path such as `"assets/icons/news.svg"`

Run `--icon-check` again on the section label and the full playlist name. Both must match. If `blocked` is true, a reject rule stopped the search; change the name or that rule's `reject`. A new rule below it will not run.

## Write the row

Insert one object beside the last row of that category, before the next category, section, or group row. Match the surrounding spacing. Do not reformat the rest of the file.

```json
{ "id": "UUxxxx", "name": "[Category] Channel or playlist title" }
```

A profile row uses `href`, `title`, and `label`:

```json
{ "name": "[Category] <a href='//www.instagram.com/name/' target='_blank'>Display name (Instagram)</a>" }
```

Keep the file's comma style. Escape quotes in the name. Match the single quotes and `target='_blank'` already used for external links.

## Example

`/playlist?list=PLxxxx` stays `PLxxxx`. `/watch?v=VIDEO&list=PLxxxx` is a video link, so it becomes the `UU` uploads playlist of that video's channel, not `PLxxxx`. `/@SomeChannel` becomes that channel's `UU` uploads playlist. `instagram.com/reel/ID` and `tiktok.com/@name/video/ID` become that account's profile, and the name ends with `(Instagram)` or `(Tiktok)`.
