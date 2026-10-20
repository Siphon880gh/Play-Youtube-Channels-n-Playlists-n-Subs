#!/usr/bin/env node
"use strict";

// Resolves YouTube links, finds playlist ids already stored in favs/*.json,
// and checks env/playlist-icons.js. Does not edit those files.

const fs = require("fs");
const path = require("path");

const VIDEO_ID = /^[A-Za-z0-9_-]{11}$/;
const CHANNEL_ID = /^UC[A-Za-z0-9_-]{22}$/;
const PLAYLIST_ID = /^[A-Za-z0-9_-]{10,}$/;

function findRoot(start) {
  let dir = start;
  for (let i = 0; i < 8; i++) {
    if (
      fs.existsSync(path.join(dir, "favs")) &&
      fs.existsSync(path.join(dir, "env", "playlist-icons.js"))
    ) {
      return dir;
    }
    const parent = path.dirname(dir);
    if (parent === dir) break;
    dir = parent;
  }
  throw new Error("Could not find the playlist app root from " + start);
}

function decodeJsonString(value) {
  try {
    return JSON.parse('"' + value + '"');
  } catch (err) {
    return value;
  }
}

function decodeHtml(value) {
  return String(value || "")
    .replace(/&amp;/g, "&")
    .replace(/&quot;/g, '"')
    .replace(/&#39;|&apos;/g, "'")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">");
}

function plainLabel(label) {
  return String(label || "").replace(/<[^>]*>/g, "");
}

function primaryCategory(name) {
  const match = plainLabel(name).match(/\[([^\]]+)\]/);
  return match ? match[1].trim() : null;
}

function uploadsPlaylistId(channelId) {
  if (!CHANNEL_ID.test(channelId)) {
    throw new Error("Not a channel id: " + channelId);
  }
  return "UU" + channelId.slice(2);
}

function exactCategoryPattern(category) {
  const text = String(category || "").trim();
  if (!text) throw new Error("Category is empty");
  const escaped = text.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
  return "(?:^" + escaped + "$|\\[" + escaped + "\\])";
}

function classify(input) {
  let raw = String(input || "").trim();
  if (!raw) throw new Error("Empty link");
  if (!/^[a-z][a-z0-9+.-]*:\/\//i.test(raw)) raw = "https://" + raw;

  let url;
  try {
    url = new URL(raw);
  } catch (err) {
    throw new Error("Not a URL");
  }

  let host = url.hostname.toLowerCase().replace(/\.$/, "").replace(/^(www|m|music)\./, "");
  if (host === "youtube-nocookie.com") host = "youtube.com";
  if (host !== "youtube.com" && host !== "youtu.be") {
    throw new Error("Not a YouTube link");
  }

  const parts = url.pathname.split("/").filter(Boolean).map(function (part) {
    try {
      return decodeURIComponent(part);
    } catch (err) {
      return part;
    }
  });
  const list = url.searchParams.get("list");
  const videoParam = url.searchParams.get("v");

  if (host === "youtu.be") {
    if (!VIDEO_ID.test(parts[0] || "")) throw new Error("Missing video id");
    return { kind: "video", videoId: parts[0] };
  }

  if (parts[0] === "playlist") {
    if (!list || !PLAYLIST_ID.test(list)) throw new Error("Playlist link has no list id");
    return { kind: "playlist", playlistId: list };
  }

  if (parts[0] === "watch") {
    if (videoParam) {
      if (!VIDEO_ID.test(videoParam)) throw new Error("Missing video id");
      return { kind: "video", videoId: videoParam };
    }
    if (list && PLAYLIST_ID.test(list)) return { kind: "playlist", playlistId: list };
    throw new Error("Missing video id");
  }

  if (["shorts", "embed", "live", "v"].indexOf(parts[0]) !== -1 && parts[1]) {
    if (!VIDEO_ID.test(parts[1])) throw new Error("Missing video id");
    return { kind: "video", videoId: parts[1] };
  }

  if (list && !videoParam) {
    if (!PLAYLIST_ID.test(list)) throw new Error("Playlist link has no list id");
    return { kind: "playlist", playlistId: list };
  }

  if (parts[0] === "channel" && parts[1]) {
    if (!CHANNEL_ID.test(parts[1])) throw new Error("Channel link has no UC id");
    return { kind: "channel", channelId: parts[1] };
  }

  if (parts[0] && parts[0].charAt(0) === "@") {
    const handle = parts[0].slice(1);
    if (!handle) throw new Error("Missing channel handle");
    return { kind: "channel", handle: handle };
  }

  if ((parts[0] === "c" || parts[0] === "user") && parts[1]) {
    return { kind: "channel", slug: parts[1], slugKind: parts[0] };
  }

  throw new Error("Unsupported YouTube link");
}

function loadApiKey(root) {
  const envPath = path.join(root, ".env");
  if (!fs.existsSync(envPath)) return "";
  const lines = fs.readFileSync(envPath, "utf8").split("\n");
  for (let i = 0; i < lines.length; i++) {
    const trimmed = lines[i].trim();
    if (!trimmed || trimmed.charAt(0) === "#") continue;
    const eq = trimmed.indexOf("=");
    if (eq === -1) continue;
    if (trimmed.slice(0, eq).trim() !== "YOUTUBE_API_KEY") continue;
    let value = trimmed.slice(eq + 1).trim();
    if (
      (value.charAt(0) === '"' && value.charAt(value.length - 1) === '"') ||
      (value.charAt(0) === "'" && value.charAt(value.length - 1) === "'")
    ) {
      value = value.slice(1, -1);
    }
    if (!value || value === "YOUR_API_KEY_HERE") return "";
    return value;
  }
  return "";
}

function fetchText(url) {
  return fetch(url, {
    redirect: "follow",
    signal: AbortSignal.timeout(20000),
    headers: {
      "User-Agent":
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
      "Accept-Language": "en-US,en;q=0.9",
      Accept: "text/html,application/xhtml+xml",
      Cookie: "SOCS=CAISNQgDEitib3FfaWRlbnRpdHlmcm9udGVuZHVpc2VydmVyXzIwMjQwOTI1LjA4X3AxGgJlbiACGgYIgL3vrgY"
    }
  }).then(function (res) {
    if (!res.ok) throw new Error("HTTP " + res.status);
    return res.text();
  });
}

function apiGet(url) {
  return fetch(url, {
    signal: AbortSignal.timeout(20000),
    headers: {
      Accept: "application/json",
      Referer: "http://localhost/"
    }
  }).then(async function (res) {
    const body = await res.json().catch(function () {
      return null;
    });
    if (!res.ok) {
      const message = body && body.error && body.error.message ? body.error.message : "HTTP " + res.status;
      throw new Error(message);
    }
    return body;
  });
}

function extractExternalChannel(html) {
  const start = html.indexOf('"channelMetadataRenderer":');
  if (start !== -1) {
    const slice = html.slice(start, start + 50000);
    const channelId = (slice.match(/"externalId":"(UC[A-Za-z0-9_-]{22})"/) || [])[1];
    const title = (slice.match(/"title":"((?:\\.|[^"\\])*)"/) || [])[1];
    if (channelId) {
      return { channelId: channelId, title: title ? decodeJsonString(title) : null };
    }
  }

  const canonical = html.match(/<link rel="canonical" href="https:\/\/www\.youtube\.com\/channel\/(UC[A-Za-z0-9_-]{22})"/);
  const og = html.match(/<meta property="og:title" content="([^"]*)"/);
  if (canonical) {
    return {
      channelId: canonical[1],
      title: og ? decodeHtml(og[1]).replace(/ - YouTube$/, "") : null
    };
  }
  return null;
}

function extractVideo(html) {
  const start = html.indexOf('"videoDetails":');
  if (start === -1) return null;
  const slice = html.slice(start, start + 12000);
  const channelId = (slice.match(/"channelId":"(UC[A-Za-z0-9_-]{22})"/) || [])[1];
  if (!channelId) return null;
  const title = (slice.match(/"title":"((?:\\.|[^"\\])*)"/) || [])[1];
  const author = (slice.match(/"author":"((?:\\.|[^"\\])*)"/) || [])[1];
  return {
    channelId: channelId,
    videoTitle: title ? decodeJsonString(title) : null,
    title: author ? decodeJsonString(author) : null
  };
}

function extractPlaylistTitle(html) {
  const metadata = html.match(/"playlistMetadataRenderer":\{"title":"((?:\\.|[^"\\])*)"/);
  if (metadata) return decodeJsonString(metadata[1]);
  const og = html.match(/<meta property="og:title" content="([^"]*)"/);
  if (!og) return null;
  return decodeHtml(og[1]).replace(/ - YouTube$/, "");
}

async function apiVideo(root, videoId) {
  const key = loadApiKey(root);
  if (!key) throw new Error("No channel id in the video page and no YouTube API key");
  const body = await apiGet(
    "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=" +
      encodeURIComponent(videoId) +
      "&key=" +
      encodeURIComponent(key)
  );
  const item = body.items && body.items[0];
  if (!item || !item.snippet || !CHANNEL_ID.test(item.snippet.channelId || "")) {
    throw new Error("YouTube API did not return a channel for " + videoId);
  }
  return {
    channelId: item.snippet.channelId,
    title: item.snippet.channelTitle || null,
    videoTitle: item.snippet.title || null,
    via: "api"
  };
}

async function apiChannel(root, query) {
  const key = loadApiKey(root);
  if (!key) throw new Error("No channel id in the page and no YouTube API key");
  const body = await apiGet(
    "https://www.googleapis.com/youtube/v3/channels?part=snippet&" + query + "&key=" + encodeURIComponent(key)
  );
  const item = body.items && body.items[0];
  if (!item || !CHANNEL_ID.test(item.id || "")) {
    throw new Error("YouTube API did not return a channel");
  }
  return {
    channelId: item.id,
    title: item.snippet && item.snippet.title ? item.snippet.title : null,
    via: "api"
  };
}

async function resolveVideo(root, videoId) {
  try {
    const html = await fetchText("https://www.youtube.com/watch?v=" + encodeURIComponent(videoId) + "&hl=en");
    const found = extractVideo(html);
    if (found) return Object.assign({ via: "page" }, found);
  } catch (err) {
    // Page fetch is the first attempt. The API fallback below covers a block or a thin page.
  }
  return apiVideo(root, videoId);
}

async function resolveChannel(root, classified) {
  if (classified.channelId) {
    let title = null;
    let via = "url";
    try {
      const html = await fetchText(
        "https://www.youtube.com/channel/" + classified.channelId + "?hl=en"
      );
      const found = extractExternalChannel(html);
      if (found && found.channelId === classified.channelId) {
        title = found.title;
        via = "page";
      }
    } catch (err) {
      title = null;
    }
    if (!title) {
      try {
        const fromApi = await apiChannel(root, "id=" + encodeURIComponent(classified.channelId));
        title = fromApi.title;
        via = "api";
      } catch (err) {
        title = null;
      }
    }
    return { channelId: classified.channelId, title: title, via: via };
  }

  const label = classified.handle ? "@" + classified.handle : classified.slugKind + "/" + classified.slug;
  const pageUrl = classified.handle
    ? "https://www.youtube.com/@" + encodeURIComponent(classified.handle) + "?hl=en"
    : "https://www.youtube.com/" + classified.slugKind + "/" + encodeURIComponent(classified.slug) + "?hl=en";

  let pageError = null;
  try {
    const html = await fetchText(pageUrl);
    const found = extractExternalChannel(html);
    if (found && CHANNEL_ID.test(found.channelId)) {
      return { channelId: found.channelId, title: found.title, via: "page" };
    }
    pageError = new Error("Channel page had no channel id");
  } catch (err) {
    pageError = err;
  }

  try {
    if (classified.handle || classified.slugKind === "c") {
      const handle = classified.handle || classified.slug;
      return await apiChannel(root, "forHandle=" + encodeURIComponent(handle));
    }
    return await apiChannel(root, "forUsername=" + encodeURIComponent(classified.slug));
  } catch (err) {
    const detail = pageError && pageError.message ? " (" + pageError.message + ")" : "";
    throw new Error("No YouTube channel found for " + label + detail);
  }
}

async function playlistTitle(playlistId) {
  const html = await fetchText(
    "https://www.youtube.com/playlist?list=" + encodeURIComponent(playlistId) + "&hl=en"
  );
  return extractPlaylistTitle(html);
}

function findExisting(root, playlistId) {
  const dir = path.join(root, "favs");
  const files = fs.readdirSync(dir).filter(function (file) {
    return file.endsWith(".json");
  });
  const hits = [];
  files.forEach(function (file) {
    let data;
    try {
      data = JSON.parse(fs.readFileSync(path.join(dir, file), "utf8"));
    } catch (err) {
      hits.push({ collection: file.replace(/\.json$/, ""), error: "Could not parse " + file });
      return;
    }
    if (!Array.isArray(data)) return;
    data.forEach(function (entry) {
      if (!entry || String(entry.id || "") !== playlistId) return;
      hits.push({
        collection: file.replace(/\.json$/, ""),
        category: primaryCategory(entry.name || ""),
        name: entry.name || "",
        id: entry.id
      });
    });
  });
  return hits;
}

function compilePattern(source, flags) {
  let pattern = String(source || "");
  let fl = flags != null ? String(flags) : "i";
  const wrapped = pattern.match(/^\/([\s\S]*)\/([a-z]*)$/);
  if (wrapped) {
    pattern = wrapped[1];
    fl = wrapped[2] || "";
  }
  fl = fl.replace(/g/g, "");
  if (!pattern) return null;
  try {
    return new RegExp(pattern, fl);
  } catch (err) {
    return null;
  }
}

function loadIconRules(root) {
  const iconsPath = path.join(root, "env", "playlist-icons.js");
  delete require.cache[require.resolve(iconsPath)];
  const loaded = require(iconsPath);
  return loaded.rules || [];
}

function matchIcon(rules, label) {
  const plain = plainLabel(label);
  for (let i = 0; i < rules.length; i++) {
    const rule = rules[i];
    if (!rule || !rule.pattern) continue;
    const re = compilePattern(rule.pattern, rule.flags);
    if (!re || !re.test(plain)) continue;
    if (rule.reject) {
      const reject = compilePattern(rule.reject, rule.flags);
      if (reject && reject.test(plain)) {
        return {
          label: label,
          matched: false,
          blocked: true,
          pattern: rule.pattern,
          reject: rule.reject
        };
      }
    }
    const icon = rule.emoji ? "emoji" : rule.font ? "font" : rule.svg ? "svg" : rule.src ? "src" : "none";
    const value = rule.emoji || rule.font || rule.src || (rule.svg ? true : "");
    return {
      label: label,
      matched: true,
      blocked: false,
      pattern: rule.pattern,
      icon: icon,
      value: value
    };
  }
  return { label: label, matched: false, blocked: false };
}

async function resolveOne(root, input) {
  let classified;
  try {
    classified = classify(input);
  } catch (err) {
    return { input: input, error: err.message };
  }

  try {
    if (classified.kind === "playlist") {
      let title = null;
      try {
        title = await playlistTitle(classified.playlistId);
      } catch (err) {
        title = null;
      }
      return {
        input: input,
        kind: "playlist",
        resolution: "playlist link",
        playlistId: classified.playlistId,
        channelId: null,
        title: title,
        existing: findExisting(root, classified.playlistId)
      };
    }

    if (classified.kind === "video") {
      const info = await resolveVideo(root, classified.videoId);
      const playlistId = uploadsPlaylistId(info.channelId);
      return {
        input: input,
        kind: "video",
        resolution: "uploads playlist",
        via: info.via,
        playlistId: playlistId,
        channelId: info.channelId,
        title: info.title || null,
        videoTitle: info.videoTitle || null,
        existing: findExisting(root, playlistId)
      };
    }

    const info = await resolveChannel(root, classified);
    const playlistId = uploadsPlaylistId(info.channelId);
    return {
      input: input,
      kind: "channel",
      resolution: "uploads playlist",
      via: info.via,
      playlistId: playlistId,
      channelId: info.channelId,
      title: info.title || null,
      existing: findExisting(root, playlistId)
    };
  } catch (err) {
    return { input: input, kind: classified.kind, error: err.message };
  }
}

function assert(condition, message) {
  if (!condition) throw new Error(message);
}

function selfTest(root) {
  const playlist = classify("https://www.youtube.com/playlist?list=PLzg85AHZsA6YMUlYeIxM80Qm_wM1UbZda");
  assert(playlist.kind === "playlist" && playlist.playlistId === "PLzg85AHZsA6YMUlYeIxM80Qm_wM1UbZda", "playlist link");

  const watch = classify("https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=PLzg85AHZsA6YMUlYeIxM80Qm_wM1UbZda");
  assert(watch.kind === "video" && watch.videoId === "dQw4w9WgXcQ" && !watch.playlistId, "watch link stays a video");

  const short = classify("https://youtu.be/dQw4w9WgXcQ?list=PLxxxxxxxxxxxxxxxxxxxx");
  assert(short.kind === "video" && short.videoId === "dQw4w9WgXcQ", "youtu.be link stays a video");

  const shorts = classify("https://www.youtube.com/shorts/dQw4w9WgXcQ");
  assert(shorts.kind === "video", "shorts link");

  const channel = classify("https://www.youtube.com/channel/UC9avFXTdbSo5ATvzTRnAVFg/videos");
  assert(channel.kind === "channel" && channel.channelId === "UC9avFXTdbSo5ATvzTRnAVFg", "channel link");
  assert(uploadsPlaylistId(channel.channelId) === "UU9avFXTdbSo5ATvzTRnAVFg", "UC becomes UU");

  const handle = classify("https://www.youtube.com/@WayneTeachesCode/videos");
  assert(handle.kind === "channel" && handle.handle === "WayneTeachesCode", "handle link");

  assert(primaryCategory("<b>[Fitness]</b> ~Bodybuilding") === "Fitness", "primary category strips HTML");
  assert(primaryCategory("[Mood/<b>Relaxed</b>] Nature") === "Mood/Relaxed", "primary category keeps slash text");
  assert(primaryCategory("Call of Duty WW2") === null, "unbracketed name has no category");

  const pattern = exactCategoryPattern("Car-buying");
  const re = new RegExp(pattern, "i");
  assert(re.test("Car-buying"), "exact section match");
  assert(re.test("[Car-buying] Your Car Angel"), "exact name match");
  assert(!re.test("Car-buying extra"), "exact pattern does not match a longer section");

  const rules = loadIconRules(root);
  const zelda = matchIcon(rules, "Zelda");
  assert(zelda.matched && zelda.icon === "svg", "Zelda icon");
  const missing = matchIcon(rules, "Zzqx-no-such-category-99");
  assert(!missing.matched && !missing.blocked, "unknown label has no icon");

  const hits = findExisting(root, "UU9avFXTdbSo5ATvzTRnAVFg");
  assert(
    hits.some(function (hit) {
      return hit.collection === "Default" && hit.category === "Asian Community/Lifestyle";
    }),
    "existing Fung Bros playlist is reported"
  );

  console.log("self-test ok");
}

async function main() {
  const root = findRoot(__dirname);
  const args = process.argv.slice(2);

  if (args[0] === "--self-test") {
    selfTest(root);
    return;
  }

  if (args[0] === "--pattern") {
    const category = args.slice(1).join(" ").trim();
    console.log(JSON.stringify({ pattern: exactCategoryPattern(category) }));
    return;
  }

  if (args[0] === "--icon-check") {
    const rules = loadIconRules(root);
    const labels = args.slice(1);
    if (!labels.length) throw new Error("Pass at least one label");
    console.log(JSON.stringify(labels.map(function (label) {
      return matchIcon(rules, label);
    }), null, 2));
    return;
  }

  if (!args.length) {
    throw new Error("Pass YouTube links, --icon-check, --pattern, or --self-test");
  }

  const results = [];
  for (let i = 0; i < args.length; i++) {
    results.push(await resolveOne(root, args[i]));
  }
  console.log(JSON.stringify(results, null, 2));
}

if (require.main === module) {
  main().catch(function (err) {
    console.error(err.message || String(err));
    process.exit(1);
  });
} else {
  module.exports = {
    classify: classify,
    uploadsPlaylistId: uploadsPlaylistId,
    exactCategoryPattern: exactCategoryPattern,
    primaryCategory: primaryCategory
  };
}
