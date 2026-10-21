// Playlist icons for the jump menu and the playlist list.
//
// Rules run in order. The first pattern that matches wins, and later rules are not tried.
// A rule can set reject to a pattern. If pattern and reject both match, that rule
// does not apply and later rules are not tried.
// The same rule is tested against:
//   - the playlist name in the playlist list (HTML tags are ignored)
//   - the section name in the jump menu (the text inside the first [brackets])
//
// pattern is a JavaScript regular expression. flags defaults to "i".
// You can also write pattern as "/Zelda/i".
// Set one of: emoji, font, svg, src.
// font is a class from a library loaded by env/icon-fonts.php.
var playlistIconRules = [
  // Specific titles before the broader theme they belong to.
  { pattern: "Zelda", svg: "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path fill='currentColor' d='M12 2.2 14.6 8H9.4L12 2.2zM6.2 21.5 8.8 15.2H3.6L6.2 21.5zM17.8 21.5 20.4 15.2h-5.2L17.8 21.5z'/></svg>" },
  { pattern: "\\bMario\\b", emoji: "🍄" },
  { pattern: "\\bDBZ\\b|Dragon Ball", font: "fas fa-dragon" },
  { pattern: "Videogame Playthroughs|Videogames|Nintendo|Playstation|God of War|Final Fantasy|Kingdom Hearts|Resident Evil|Cyberpunk|Call of Duty|Half[- ]Life|Chrono Trigger|EarthBound|Duck Tales|Demon.?s Souls|Xenoblade|Torchlight|inFamous|Fenyx|Kid Icarus|\\bHalo\\b|Marvel's Avengers", font: "fas fa-gamepad" },
  { pattern: "Marvel", font: "fas fa-mask" },

  { pattern: "Car-repair", font: "fas fa-wrench" },
  { pattern: "Police", font: "fas fa-video" },
  { pattern: "\\bDriving\\b|Car-buying|Car-shows", font: "fas fa-car" },
  { pattern: "Cooking|Restaurants|\\bFood\\b|\\bMeals\\b|Nutrition|Holistic", font: "fas fa-utensils" },

  { pattern: "Sleepy Cat", emoji: "😴" },
  { pattern: "Yoga|Medit|\\bHealing\\b", reject: "Sleepy Cat", font: "fas fa-spa" },
  { pattern: "Physical\\.Therapy|\\bmedical\\b|nursing|\\bICU\\b", font: "fas fa-notes-medical" },
  { pattern: "Nootropics", font: "fas fa-capsules" },
  { pattern: "Workout|Fitness|Flexibility", font: "fas fa-dumbbell" },
  { pattern: "Mewing", font: "fas fa-smile" },

  { pattern: "Studying music|Coding music|\\bIntense\\b|Keep the beat going|Coffee Shop Chill Playlist", font: "fas fa-music" },
  { pattern: "Music", font: "fas fa-music" },
  { pattern: "Mood/Music|\\bMusic\\b|Concerts", font: "fas fa-music" },
  { pattern: "Mood/Sleep|\\bYawning\\b", emoji: "😴" },
  { pattern: "Concentration|Coffee Shop", reject: "Studying music|Coding music|\\bIntense\\b|Keep the beat going|Coffee Shop Chill Playlist|\\bMusic\\b", emoji: "🎧" },
  { pattern: "Medieval", font: "fas fa-hat-wizard" },
  { pattern: "Nostalgia|\\bOldies\\b", font: "fas fa-history" },
  { pattern: "\\bRelaxed\\b|Nature Relaxation", font: "fas fa-leaf" },
  { pattern: "Scenic/Animals|\\bAnimals\\b|\\bPets\\b", font: "fas fa-paw" },
  { pattern: "Scenic|Adventures", font: "fas fa-mountain" },
  { pattern: "Motivation", font: "fas fa-fire" },

  { pattern: "\\bSins\\b|deliverance|\\bprayers\\b|\\bLOA\\b", font: "fas fa-praying-hands" },
  { pattern: "Warfare", font: "fas fa-shield-alt" },
  { pattern: "UFOs|\\bUFO\\b", emoji: "👽" },
  { pattern: "Ancient|Anomalies", font: "fas fa-landmark" },
  { pattern: "Paranormal|Astral|Conspiracy|Cryptid|Cryptozoology|Mysteries", font: "fas fa-ghost" },

  { pattern: "\\bNews\\b", src: "assets/icons/news.svg" },
  { pattern: "Cybersecurity|\\bSecurity\\b", font: "fas fa-user-shield" },
  { pattern: "\\biOS\\b", font: "fab fa-apple" },
  { pattern: "Programming/JS|\\bJS\\b", font: "fab fa-js" },
  { pattern: "Programming|Programmer|\\bUML\\b|Script Automation", font: "fas fa-code" },
  { pattern: "Loopable Learning|Education|teaching|Videoediting", font: "fas fa-book-reader" },
  { pattern: "\\bNLP\\b", font: "fas fa-brain" },
  { pattern: "Self-Improvement|Productive|Nofap", font: "fas fa-seedling" },
  { pattern: "\\bbusiness\\b", font: "fas fa-briefcase" },

  { pattern: "Relationships|\\bGF\\b", font: "fas fa-heart" },
  { pattern: "reddit", font: "fab fa-reddit-alien" },
  { pattern: "Reacts", font: "fas fa-eye" },
  { pattern: "Scambait|\\bScammers\\b", font: "fas fa-user-secret" },
  { pattern: "Survivalist|Prepping|\\bDefense\\b", font: "fas fa-campground" },
  { pattern: "Japan", font: "fas fa-torii-gate" },
  { pattern: "\\bTravel\\b|Walking", font: "fas fa-plane" },
  { pattern: "Asian Community", font: "fas fa-globe-asia" },

  { pattern: "Comedy", font: "fas fa-laugh" },
  { pattern: "Films|Movies|Trailers|\\bMatrix\\b|Recaps", font: "fas fa-film" },
  { pattern: "ASMR", font: "fas fa-volume-up" },
  { pattern: "Products", font: "fas fa-box-open" },
  { pattern: "Pronounce", font: "fas fa-language" },
  { pattern: "Opinion|\\bTheory\\b", font: "fas fa-comments" },
  { pattern: "Curiosity|\\bscience\\b", font: "fas fa-lightbulb" },
  { pattern: "Entertaining", font: "fas fa-star" },
  { pattern: "Lifestyle/Events|\\bEvents\\b", font: "fas fa-ticket-alt" },
  { pattern: "Lifestyle", font: "fas fa-home" },
  { pattern: "Env:", font: "fas fa-spa" },
  { pattern: "\\bRelease\\b", font: "fas fa-feather-alt" },
  { pattern: "\\bSx\\b|Sxy", font: "fas fa-heart" },
  { pattern: "(?:^Legal Cases$|\\[Legal Cases\\])", font: "fas fa-gavel" },
  { pattern: "(?:^Documentary/FairyTales$|\\[Documentary/FairyTales\\])", font: "fas fa-book-open" },
  { pattern: "(?:^AI Leverage$|\\[AI Leverage\\])", font: "fas fa-robot" }
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

  function safeSrc(src) {
    var value = String(src || "").trim();
    if (!value || /^\s*javascript:/i.test(value) || /^\s*data:/i.test(value)) return "";
    if (/^(?:https?:)?\/\//i.test(value) || /^\.\.?\//.test(value) || /^[a-z0-9_./-]+$/i.test(value)) {
      return value;
    }
    return "";
  }

  function innerFromRule(rule) {
    if (rule.emoji != null && String(rule.emoji) !== "") return escapeText(rule.emoji);
    if (rule.font) {
      var cls = String(rule.font).trim();
      if (!/^[\w\-\s]+$/.test(cls)) return "";
      return "<i class=\"" + cls + "\" aria-hidden=\"true\"></i>";
    }
    if (rule.svg) return String(rule.svg);
    if (rule.src) {
      var src = safeSrc(rule.src);
      if (!src) return "";
      return "<img src=\"" + escapeAttr(src) + "\" alt=\"\" class=\"playlist-icon-img\">";
    }
    return "";
  }

  function resolveIconInnerHtml(name, rules) {
    var label = plainLabel(name);
    var list = rules || root.playlistIconRules || [];
    for (var i = 0; i < list.length; i++) {
      var rule = list[i];
      if (!rule || !rule.pattern) continue;
      if (!patternHits(rule, "pattern", label)) continue;
      // A reject match ends the search. Later rules must not replace it.
      if (patternHits(rule, "reject", label)) return "";
      return innerFromRule(rule);
    }
    return "";
  }

  function playlistIconHtml(name, rules) {
    var inner = resolveIconInnerHtml(name, rules);
    if (!inner) return "";
    return "<span class=\"playlist-icon custom-icon\" aria-hidden=\"true\">" + inner + "</span>";
  }

  root.playlistIconRules = playlistIconRules;
  root.resolveIconInnerHtml = resolveIconInnerHtml;
  root.playlistIconHtml = playlistIconHtml;
})(typeof window !== "undefined" ? window : global);

if (typeof module !== "undefined" && module.exports) {
  module.exports = {
    rules: playlistIconRules,
    resolveIconInnerHtml: global.resolveIconInnerHtml,
    playlistIconHtml: global.playlistIconHtml
  };
}
