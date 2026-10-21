// Playlist row actions (links beside a playlist) driven by category rules.
//
// Rules run in order. The first pattern that matches wins.
// A rule can set reject to a pattern. If pattern and reject both match, that rule
// does not apply and later rules are not tried.
// The same rule is tested against:
//   - the playlist name in the playlist list (HTML tags are ignored)
//   - the section / category label (text inside the first [brackets], without brackets)
//
// pattern is a JavaScript regular expression. flags defaults to "i".
// url may include {channel}: the channel/playlist display name with the leading
// [Category] stripped, then URL-encoded for a query string.
// label is the text revealed on hover. icon is optional Font Awesome 5 free classes.
var playlistActionRules = [
  {
    pattern: "(?:^Stocks$|\\[Stocks\\])",
    label: "Audit",
    icon: "fas fa-search-dollar",
    url: "https://www.google.com/search?q=what+people+say+about+youtuber+{channel}&udm=50"
  }
];

(function (root) {
  function plainLabel(label) {
    return String(label || "").replace(/<[^>]*>/g, "");
  }

  function escapeText(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
  }

  function escapeAttr(value) {
    return escapeText(value).replace(/"/g, "&quot;");
  }

  function channelDisplayName(name) {
    return plainLabel(name).replace(/^\[[^\]]+\]\s*/, "").trim();
  }

  function compilePattern(rule, field) {
    var key = field || "pattern";
    var cacheKey = key === "pattern" ? "_re" : "_re_" + key;
    if (Object.prototype.hasOwnProperty.call(rule, cacheKey)) return rule[cacheKey];

    var source = String(rule[key] || "");
    var flags = rule.flags != null ? String(rule.flags) : "i";
    var wrapped = source.match(/^\/([\s\S]*)\/([a-z]*)$/);
    if (wrapped) {
      source = wrapped[1];
      flags = wrapped[2] || "";
    }
    flags = flags.replace(/g/g, "");

    try {
      rule[cacheKey] = source ? new RegExp(source, flags) : null;
    } catch (err) {
      rule[cacheKey] = null;
    }
    return rule[cacheKey];
  }

  function patternHits(rule, field, label) {
    if (!rule || rule[field] == null || String(rule[field]) === "") return false;
    var re = compilePattern(rule, field);
    if (!re) return false;
    if (re.global) re.lastIndex = 0;
    return re.test(label);
  }

  function ruleMatches(rule, name, category) {
    if (!rule || !rule.pattern) return false;
    var labels = [];
    var plainName = plainLabel(name);
    if (plainName) labels.push(plainName);
    if (category) labels.push(String(category));
    var hit = false;
    for (var i = 0; i < labels.length; i++) {
      if (patternHits(rule, "pattern", labels[i])) {
        hit = true;
        break;
      }
    }
    if (!hit) return false;
    for (var j = 0; j < labels.length; j++) {
      if (patternHits(rule, "reject", labels[j])) return false;
    }
    return true;
  }

  function buildActionUrl(rule, name) {
    var template = String(rule.url || "");
    if (!template) return "";
    var channel = channelDisplayName(name);
    if (!channel) return "";
    return template.replace(/\{channel\}/g, encodeURIComponent(channel));
  }

  function resolvePlaylistAction(name, category, rules) {
    var list = rules || root.playlistActionRules || [];
    for (var i = 0; i < list.length; i++) {
      var rule = list[i];
      if (!ruleMatches(rule, name, category)) continue;
      var href = buildActionUrl(rule, name);
      if (!href) continue;
      return {
        href: href,
        label: String(rule.label || "Audit"),
        icon: String(rule.icon || "fas fa-search")
      };
    }
    return null;
  }

  function playlistActionHtml(name, category, rules) {
    var action = resolvePlaylistAction(name, category, rules);
    if (!action) return "";
    var iconClass = String(action.icon || "").trim();
    if (!/^[\w\-\s]+$/.test(iconClass)) iconClass = "fas fa-search";
    return (
      '<a class="playlist-action" href="' + escapeAttr(action.href) + '" target="_blank" rel="noopener noreferrer" ' +
      'onclick="event.stopPropagation();" title="' + escapeAttr(action.label) + '" aria-label="' + escapeAttr(action.label) + '">' +
      '<i class="' + iconClass + '" aria-hidden="true"></i>' +
      '<span class="playlist-action-label">' + escapeText(action.label) + "</span>" +
      "</a>"
    );
  }

  root.playlistActionRules = playlistActionRules;
  root.channelDisplayName = channelDisplayName;
  root.resolvePlaylistAction = resolvePlaylistAction;
  root.playlistActionHtml = playlistActionHtml;
})(typeof window !== "undefined" ? window : global);

if (typeof module !== "undefined" && module.exports) {
  module.exports = {
    rules: playlistActionRules,
    channelDisplayName: global.channelDisplayName,
    resolvePlaylistAction: global.resolvePlaylistAction,
    playlistActionHtml: global.playlistActionHtml
  };
}
