---
name: adding-youtube-playlists
description: >-
  Use when the user gives YouTube channel links, video links, or playlist
  links to add, file, sort, or categorize in this playlist app, including
  turning a channel or video into its latest-uploads playlist and choosing
  a playlist icon.
---

# Adding YouTube playlists

Channel and video links become that channel's uploads playlist (latest videos). A link that is already a playlist stays that playlist. Each result is inserted into the matching category in `favs/*.json` unless that category already has the same playlist id. Every added row needs an icon rule in `env/playlist-icons.js`.

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
| `/channel/UC...`, `/@handle`, `/c/...`, `/user/...` | Uploads playlist of that channel |

An uploads playlist id is the channel id with the leading `UC` replaced by `UU`. That playlist is the channel's videos from newest to oldest. Do not call `search.list`. Do not invent an id when the resolver returns `error`; skip that link and report it.

`resolution: "playlist link"` means the id was kept. `resolution: "uploads playlist"` means it was derived from the channel.

## Category

Read `favs/*.json` in this run. Do not rely on a remembered category list.

A collection is one JSON file. `Default.json` is the collection the shuffler loads. A category is the first `[bracket]` in an entry name after HTML tags are removed. The app turns that bracket into the section header. A row with no `id` is a separator, not a playlist. `Playthroughs.json` usually has no brackets; those names are the rows themselves.

1. Use the collection and category the user named. Copy an existing category string exactly, including capitals and slashes.
2. Otherwise pick the existing category whose label fits the channel or playlist, in the collection where that category already appears.
3. If that category exists in more than one collection, use the collection whose neighboring rows are the same kind of channel.
4. Use `Playthroughs.json` for a game playthrough, and match that file's unbracketed names.
5. Add a new `[Category]` only when no existing category fits. Put it in `Default.json` unless the user named another collection. Do not create a new collection file unless the user asks.

Display name, matching the neighbors in that category:

```text
[Category] Channel or playlist title
```

Use the resolver's `title` (the channel name, or the playlist title when the link was already a playlist). Keep a neighbor's `(Shuffle)`, HTML, or emoji only when that category already uses it.

## Duplicates

The resolver's `existing` array lists every saved copy of that playlist id.

Drop the link when the chosen collection already has that id in the chosen category. For an unbracketed collection, the file is the category: drop the id when it already appears anywhere in that file. Tell the user which entry was already there.

The same id in a different category is not a duplicate. Two input links that resolve to the same id and the same category are one add.

## Icon

Read the header of `env/playlist-icons.js`. Rules run in order. The first matching pattern wins. If `pattern` and `reject` both match, that rule does not apply and later rules are not tried.

The app tests a rule against both the playlist name and the section label (the text inside the first brackets, without the brackets).

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

Insert one object beside the last row of that category, before the separator that follows the group. Match the surrounding spacing. Do not reformat the rest of the file.

```json
{ "id": "UUxxxx", "name": "[Category] Channel or playlist title" }
```

Keep the file's comma style. Escape quotes in the name.

## Example

`/playlist?list=PLxxxx` stays `PLxxxx`. `/watch?v=VIDEO&list=PLxxxx` is a video link, so it becomes the `UU` uploads playlist of that video's channel, not `PLxxxx`. `/@SomeChannel` becomes that channel's `UU` uploads playlist.
