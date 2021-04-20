<script>window.parent.prependPath="./";</script>
<?php $prependPath="./"; ?>
<script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<link href="<?php echo $prependPath; ?>assets/css/favs.css" rel="stylesheet"></link>
<script src="<?php echo $prependPath; ?>assets/js/favs.js"></script>
<?php include($prependPath."assets/templates/favs.php"); ?>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
<div id="favs">
  <div class="header">
    <?php
    /**
     * @events
     *
     * Secret gesture on "Watch videos from Youtube channels"
     * and "Playlist" headers that open more Fav collections.
     * In respective order, the combination is
     * click x2, then click x2.
     * 
    */
    ?>
    <a href="javascript:void(0);" onclick="window.parent.More.counter('Favheader');">Watch videos from Youtube channels:</a>
    <div class="float-right float-right-buttons" style="padding-right: 5px;">
      <i id="random" class="fa fa-random clickable" onclick="RandomPlaylist.select();" style="margin-left:3px;"></i>
      <div style="width:1px; height:10px;"></div>
      <i id="manual" class="fa fa-cloud-upload-alt clickable" onclick="ManualPlaylist.prompt();"></i>
    </div> <!-- /float-right -->
  </div> <!-- /header -->
  <ul class="playlists-target">
  </ul>
</div>
<script>

var playlistList = [
  {id: 'UU9avFXTdbSo5ATvzTRnAVFg', name: '[Asian Community] Fung Bros'},
  {id: 'UUK7IIV6Q2junGSdYK3BmZMg', name: '[Celebs] TMZ'},
  {id: 'UU4khWMmMODpHD0Bt-6HPO2A', name: '[Comedy] Brent Pella'},
  {id: 'UU8OJagbECdNfwOpAxHPxSgg', name: '[Comedy] Cassady Campbell'},
  {id: 'UUcoDpbA4c9HQY0AjKHSlKLg', name: '[Comedy] Cer Spence'},
  {id: 'UUPHeAtHSXnymNh8b_Kkj2rw', name: '[Comedy] Cherdleys'},
  {id: 'UUR0VLWitB2xM4q7tjkoJUPw', name: '[Comedy] Creations Ross'},
  {id: 'UUGAY8t6KCq0tXyBFfjXeHZw', name: '[Comedy] Ed Bassmaster'},
  {id: 'UUXHz8Ai5458HM9XK3n-UhTQ', name: '[Comedy] Eric Kanevsky'},
  {id: 'UUaMeP81UxZ8YVsREVS1aBHA', name: '[Comedy] Everything Pokelawls'},
  {id: 'UUCIVKWTfWQXrSPdIxnNlZ4Q', name: '[Comedy] Jon Bravo'},
  {id: 'UUkhxWF5CTMUgxneqAFP96LQ', name: '[Comedy] Nelk'},
  {id: 'UUdcUmdOxMrhRjKMw-BX19AA', name: '[Comedy] Niko Omilana'},
  {id: 'UUxOGHW-aqciBe5Wjq8ltzOg', name: '[Comedy] Ownage Pranks'},
  {id: 'UUWGmhDFi3fB5DA1nuVRsPIQ', name: '[Comedy] Reactistan'},
  {id: 'UUqYlcD2MurA9YWe7D911w7A', name: '[Comedy] Ross Smith aka Crazy Grandma'},
  {id: 'UUhQvODKsbmt-A8bgLauCMqA', name: '[Comedy] Steezy Kane'},
  {id: 'UUSrnmu3W6YXWU_85DKT5arg', name: '[Comedy] ThatWasEpic'},
  {id: 'UUvkdpLN1agXoiRWEWTtXqjw', name: '[Comedy] TopNotch Idiots'},
  {id: 'UUpMNRVfCv9B_MdfJe6ij9Aw', name: '[Comedy/Clips] Ghrey'},
  {id: 'UUizbjdVRrT_lw85kV7iRUAQ', name: '[Comedy/Clips] Twitch Lulz'},
  {id: 'UUp5x5kxCgg_A220jNIsLyfA', name: '[Conspiracy.Theories/Christian] A Call for an Uprising (Propaganda)'},
  {id: 'UUo8fiE2-s0SZu6Onb8lNLMQ', name: '[Conspiracy.Theories/Christian] Scariest Movie Ever (Propaganda)'},
  {id: 'UUGe_v1bnmWI0QBoK1ArnLmg', name: '[Conspiracy.Theories/Christian] Spencer Smith (Propaganda)'},
  {id: 'UUeLc4heXqG9dtL7jiPHMBfQ', name: '[Curiosity/news/China] The Epoch Times (Propaganda)'},
  {id: 'UUgFP46yVT-GG4o1TgXn-04Q', name: '[Curiosity/news/China] China Uncensored (Propaganda)'},
  {id: 'UU8Ioh4atNDO5a0Vy2qFmauQ', name: '[Curiosity/news/China] NTD (Propaganda)'},
  {id: 'UUBOqkAGTtzZVmKvY4SwdZ2g', name: '[Curiosity/news/China] NTD - China in Focus (Propaganda)'},
  {id: 'UUG8yy4W8nBbTle9YSpCqXeg', name: '[Curiosity/news/China] Crossroads with Joshua Philipp'},
  {id: 'UUZz8q2tZC7QuQiFwPKQ4Bug', name: '[Curiosity/news/China] TFIglobal'},
  {id: 'UU_gUM8rL-Lrg6O3adPW9K1g', name: '[Curiosity/news/China] Wion'},
  {id: 'UUknLrEdhRCp1aegoMqRaCZg', name: '[Curiosity/news/global] DW News'},
  {id: 'UU_7vFlErTHxVD-IFNB-BFCg', name: '[Curiosity/news/local] America Uncovered'},
  {id: 'UUHS7hIC7dYmul82KJ9p57Dw', name: '[Curiosity/reddit] Panda Reddit'},
  {id: 'UUTUJrMRaPvlaqkQ6YpVM08w', name: '[Curiosity/reddit] Radio TTS'},
  {id: 'UUBkiU4KW2amn5lI480LASgQ', name: '[Curiosity/reddit] Reddit Aliens'},
  {id: 'UU2sabBFKKPFcz8tg0lZwzvg', name: '[Curiosity/reddit] Reddit on Tap'},
  {id: 'UUgSmBJD9imASmJRleycTCwQ', name: '[Curiosity/reddit] Sir Reddit'},
  {id: 'UUYUunpzH7_WtYwvss2Ja9GQ', name: '[Curiosity/reddit] Storytime with Reddit'},
  {id: 'UUegtkvhLG9XYzWfRR99ateQ', name: '[Curiosity/reddit] Updoot'},
  {id: 'UUV3Nm3T-XAgVhKH9jT0ViRg', name: '[Curiosity/topics] AJ+'},
  {id: 'UUqYPhGiB9tkShZorfgcL2lA', name: '[Curiosity/topics] What I\'ve Learned'},
  {id: 'UUKaOcwliIgmzP87XMT0Arpw', name: '[Entertainment/Anime/DBZ] ExperGamez'},
  {id: 'UUgIqhB8oD5SZ0yUSp71VCOQ', name: '[Entertainment/Anime/DBZ] Geekdom101'},
  {id: 'UUObfIFcPxDOsyNXTG3ISyBg', name: '[Entertainment/Anime/DBZ] MaSTAR Media'},
  {id: 'UUFNZ1cFq0TI6oL1UjFOv2Xw', name: '[Entertainment/Anime/DBZ] UnrealEntGaming'},
  {id: 'UUbDM52R_HA2UweV4IcwOT_w', name: '[Entertainment/Videogames] GenerationGapGaming'},
  {id: 'PLzg85AHZsA6aMazn2DAMV-mscaVPONISA', name: '[Mood/Concentration] ~Coding music'},
  {id: 'PLzg85AHZsA6YMUlYeIxM80Qm_wM1UbZda', name: '[Mood/Concentration] ~Busy Sounds'},
  {id: 'PLzg85AHZsA6ZTv128rwSO691EMOVsOih1', name: '[Mood/Concentration] ~Time-Lapsed Music'},
  {id: 'UU3gWv-0A3qEeFBJESlsJa0g', name: '[Mood/Motivation] HES Motivation'},
  {id: 'UUrTlvJkxo5LmCvwz8k3aXLA', name: '[Mood/Motivation/Workout] Beast Motivation'},
  {id: 'UUNMJLu39B4DPjSM_USGYTUw', name: '[Mood/Motivation/Workout] Alpha Motivation'},
  {id: 'PLzg85AHZsA6Z3jwM_jYHoi40AMhPUeU7_', name: '[Mood/Music/Misc] Intense'},
  {id: 'PLzg85AHZsA6Z0dmqF0A8LxVf1ojZZwfUm', name: '[Mood/Music/Misc] Keep the beat'},
  {id: 'UUDsYDtM5BNXz-6OzZlad4ew', name: '[Mood/Music/Video-Mashup] Lana Del Rey 1'},
  {id: 'UUpqUuir75c-fuyvpMvyxbqQ', name: '[Mood/Music/Video-Mashup] Lana Del Rey 2'},
  {id: 'UUpqUuir75c-fuyvpMvyxbqQ', name: '[Mood/Music/Video-Mashup] Lana Del Rey 2'},
  {id: 'UUYNGYQiB3tvOv8uza8cAjJQ', name: '[Mood/Music] Aluna George'},
  {id: 'UU5-gWZXAQqSGVfPHkA7NRiQ', name: '[Mood/Music] Bebe Rexha'},
  {id: 'UUiGm_E4ZwYSHV3bcW1pnSeQ', name: '[Mood/Music] Billie Eilish'},
  {id: 'PLUBfAAO0X0TfEgkI6c-nS8vcJ5UDiFnKI', name: '[Mood/Music] Halsey'},
  {id: 'UU-J-KZfRV8c13fOCkhXdLiQ', name: '[Mood/Music] Dua Lipa'},
  {id: 'UU4738z_3cmq7Kppo7q8-6ZA', name: '[Mood/Scenic/Adventures] Brandon Gross'},
  {id: 'UUg8VTeeN4BglqmI1_CBlABg', name: '[Mood/Scenic/Animals] Ocean Conservation Namibia'},
  {id: 'PLLjLR4wvdjMbpvWfO3Sz6fEEequl1hQZS', name: '[Mood/Scenic/Driving] J Utah'},
  {id: 'PLzg85AHZsA6Z8EmcIryHG8yM7_qvkFCXE', name: '[Mood/Sleep] Boring'},
  {id: 'PLzg85AHZsA6asto33Xtovd4vvnZku-UrC', name: '[Mood/Sleep] Yawning'},
  {id: 'PLzg85AHZsA6aPadUZd7IuCaCtXEDPqQLd', name: '[Mood/Sleep] White Noise'},
  {id: 'PLzg85AHZsA6ZR2sayII1ahKM5jiwQHTAY', name: '[Mood/Sleep] Tips'},
  {id: 'UUi8e0iOVk1fEOogdfu4YgfA', name: '[Movies] Movieclips Trailers'},
  {id: 'UUT0dmfFCLWuVKPWZ6wcdKyg', name: '[Opinion/Doc] James Jani'},
  {id: 'UU3WGuV8xgGZAk2B2itJqgDg', name: '[Opinions/Films] Let Me Explain'},
  {id: 'UUkFbAywlXz4W0BCle2eFyUw', name: '[Opinions/Entertainment] 3CFilms'},
  {id: 'UUfwE_ODI1YTbdjkzuSi1Nag', name: '[Opinions/Entertainment] TheQuartering'},
  {id: 'UUdC0An4ZPNr_YiFiYoVbwaw', name: '[Opinions/Clips] Daily Dose Of Internet'},
  {id: 'UUuJyaxv7V-HK4_qQzNK_BXQ', name: '[Opinions/Videogames] BeatEmUps'},
  {id: 'UUvNerWQ2d5YmFFZDkRKR09Q', name: '[Opinions/Videogames] Chi Guy'},
  {id: 'UUfAPTv1LgeEWevG8X_6PUOQ', name: '[Opinions/Videogames] GameXplain'},
  {id: 'UUvBHFNnPXt_re1WSdsennJg', name: '[Opinions/Videogames] Hungrybox'},
  {id: 'UUtyONPsbFgoDvVIVteK1dHg', name: '[Opinions/Videogames] Ircha Gaming'},
  {id: 'UUDgK6UKKrwcECeF-PcAd12A', name: '[Opinions/Videogames] MaskedNintendoBandit'},
  {id: 'UUcZ959PMwCmSG1mP1gMmDBw', name: '[Opinions/Videogames] Mish Koz'},
  {id: 'UUOuddH5GyBXp-_tv_ASdp_A', name: '[Opinions/Videogames] Nick Robinson'},
  {id: 'UUA5RGaQc-a8tIX_AqTTmWdw', name: '[Opinions/Videogames] RGT 85'},
  {id: 'UUImU34iZhak7TN0IgNtUTdw', name: '[Opinions/Videogames] Robo Rob Gaming'},
  {id: 'UU5UYMeKfZbFYnLHzoTJB1xA', name: '[Opinions/Videogames] Schaffrillas Productions'},
  {id: 'UU0VVYtw21rg2cokUystu2Dw', name: '[Opinions/Videogames] SmallAnt'},
  {id: 'UUoIXnB865l9Ex9zs4OIXTdQ', name: '[Opinions/Videogames] Spawn Wave'},
  {id: 'UU8wucBti_VxVFhUAzOLyMHw', name: '[Opinions/Videogames] SwitchForce'},
  {id: 'UUyy7dZhgfeMMctSoo3wDXlQ', name: '[Opinions/Videogames] Thomas Game Docs'},
  {id: 'UU4It_xPxQyCpyTJshlAQSgA', name: '[Opinions/Videogames/Zelda] 100 Percent Zelda'},
  {id: 'UUOkE7SxzwKUII9CxfggMSIw', name: '[Opinions/Videogames/Zelda] Commonwealth Realm'},
  {id: 'UUQAi3Q1KiKlOERMT6Uvnsvw', name: '[Opinions/Videogames/Zelda] Croton'},
  {id: 'UUU3C2fxkkG2qPiVfxZH_7tw', name: '[Opinions/Videogames/Zelda] GameOver Jesse'},
  {id: 'UUftyIWbjCPJs4KSrFciMqkA', name: '[Opinions/Videogames/Zelda] NintendoBlackCrisis'},
  {id: 'UUQAi3Q1KiKlOERMT6Uvnsvw', name: '[Opinions/Videogames/Zelda] Zelda Croton'},
  {id: 'UUudx6plmpbs5WtWvsvu-EdQ', name: '[Opinions/Videogames/Zelda] Zelda Zeltik'},
  {id: 'UUJfJWct8jN1RpCuVWk3zHTA', name: '[Opinion] Daryl Talks Games'},
  {id: 'UUDBCCMlFD-hArPbWv38FINA', name: '[Opinion] Daz Games'},
  {id: 'UUhc-m3saf8K2oJHDSsnsj_A', name: '[Opinion] Elvis The Alien'},
  {id: 'UUex87CEaoqw365i63aKxepQ', name: '[Opinion] Ice1Cube'},
  {id: 'UUr0XW6TU9XVWlWPpEwEyf3g', name: '[Opinion] Jamari'},
  {id: 'UUwM8W2IEsCLRQWc6krOlRVw', name: '[Opinion] James Marriott'},
  {id: 'UUoLUji8TYrgDy74_iiazvYA', name: '[Opinion] Jarvis Johnson'},
  {id: 'UUyhOl6uRlxryALlT5yifldw', name: '[Opinion] J.J. McCullough'},
  {id: 'UU7zsxKqd5MicTf4VhS9Y74g', name: '[Opinion] Kurtis Conner'},
  {id: 'UUhhyfYkhuVrzmvDr-WqpDSw', name: '[Opinion] Not Even Emily'},
  {id: 'UUUWhqBJfvkZogLBxFttN4_g', name: '[Opinion] Omni'},
  {id: 'UUittVh8imKanO_5KohzDbpg', name: '[Opinion] Paul Joseph Watson (Propaganda)'},
  {id: 'UUq6VFHwMzcMXbuKyG7SQYIg', name: '[Opinion] penguinz0'},
  {id: 'UUlFSU9_bUb4Rc6OYfTt5SPw', name: '[Opinion] Philip DeFranco'},
  {id: 'UUG6zBb8GZKo1XZW4eHdg-0Q', name: '[Opinion] PointCrow'},
  {id: 'UUsvUzBAxu2t5e_qOEr6iczQ', name: '[Opinion] Psych IRL'},
  {id: 'UUtMVHI3AJD4Qk4hcbZnI9ZQ', name: '[Opinion] SomeOrdinaryGamers'},
  {id: 'UUzQUP1qoWDoEbmsQxvdjxgQ', name: '[Opinion] Joe Rogan'},
  {id: 'UUnxGkOGNMqQEUMvroOWps6Q', name: '[Opinion] Joe Rogan (JRE Clips)'},
  {id: 'UU3jdnIP2u5hCJpVZ-TuDrCg', name: '[Opinion] Whang!'},
  {id: 'UUeR0n8d3ShTn_yrMhpwyE1Q', name: '[Reviews/Food] TheReportOfTheWeek aka ReviewBrah'},
  {id: 'UU_0osV_nf2b0sIbm4Wiw4RQ', name: '[Scammers] IRLrosie'},
  {id: 'UUBNG0osIBAprVcZZ3ic84vw', name: '[Scammers] Jim Browning'},
  {id: 'UUca2961Ton2js_f9IDXK9Wg', name: '[Scammers] Trilogy Media'},
  {id: 'UUwNPa8fSXzzAZuT9859GVhg', name: '[Travel/China] AVD China'},
  {id: 'UUxDZs_ltFFvn0FDHT6kmoXA', name: '[Travel/China] Bald and bankrupt'},
  {id: 'UUhvithwOECK5g_19TjldMKw', name: '[Travel/China] Laowhy86'},
  {id: 'UUGXQvAsqUJrthTvTorUAGAw', name: '[Travel/California] Vince Nguyen'},
  {id: 'UUMonnTokw9e--l7wxeB3yoA', name: '[Travel/Japan] ImJasmine: Twitch Highlights'},
  {id: 'UUJb5MIGYcoXHKqMtom0kfhw', name: '[Travel/Socal] Bert Isobe'},
  {id: 'UU-EwLNFl_BETcI1naM_6FcQ', name: '[Travel/Socal] CA Travel Tips'},
  {id: 'UUWdcosWNz26bf9Jri8DTnow', name: '[Travel/Socal] Christophe Choo'},
  {id: 'UUATNyKWUqlFenle37L4qk3Q', name: '[Travel/Socal] SoCalExploring'},
  {id: 'UUw8_bi1G4duPevdfktipGOA', name: '[Travel/Socal] Yellow Productions'},
  {id: 'UUKr68ZJ4vv6VloNdnS2hjhA', name: '[Travel/global] Harald Baldr'},
  {id: 'UUpQu57KgT7gOoLCAu3FFQsA', name: '[Travel/global] Voice of North Korea'},
  {id: 'UUyEd6QBSgat5kkC6svyjudA', name: '[Travel/food] Mark Wiens'},
  {id: 'UU3Vuq4Q1bKFtAiKYlwRv3oA', name: '[Travel] Peter Santenello'},
  {id: 'UUl7mAGnY4jh4Ps8rhhh8XZg', name: '[Travel] Serpentza'},
  {id: 'UU12jo_pTYrNmDAscoqfYIBg', name: '[Travel] Tall Travels'},
  {id: 'UUO5B9ZW7miyP97QN8NtU48w', name: '[Travel] Way Beyond Pad Thai'}
];

$(document).ready(function(){
  function changeVideo(event) {
    event.preventDefault();
    var id = $(event.target).attr("data-playlist-id") ;
    window.parent.urlChange.playlist(event, id);
  }
  var $playlistContainer = $(".playlists-target");

  for(var i =0; i<playlistList.length; i++) {
    var playlistObject = playlistList[i];
    var id = playlistObject.id;
    var name = playlistObject.name;

    var li = $("<li></li>")
    var a = $("<a></a>");

    // Add interactivity
    a.attr("href","javascript:void(0);");
    a.attr("data-playlist-id",id);
    a.click(changeVideo);
    
    // Change text
    a.text(name);

    // Append a into li
    li.append(a);

    // Append to playlist list
    $playlistContainer.append(li);
  }

  window.parent.resizeIframeFavs();
});
</script>