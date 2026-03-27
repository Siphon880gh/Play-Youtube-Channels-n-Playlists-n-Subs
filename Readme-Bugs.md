Problem 1:
In late March 2026, the YouTube IFrame Player API embed began behaving differently in this app, causing videos to crash after several videos had already played.

Status: Solved, although Problem 2 notes a remaining limitation that this fix did not fully address.


---

Problem 2:
Video recovery for the YouTube IFrame Player API is now working, but stall detection is still fairly slow. The watchdog only checks every 4 seconds, waits about 10 to 15 seconds before treating playback as stalled, and also uses a 15-second recovery cooldown. As a result, a broken video can take a noticeable amount of time before restart begins.

Recommendation:
Tune the watchdog to react more quickly. A later pass could reduce the check interval from 4s to 2s, lower the `PLAYING` stall threshold from 12s to 6s, lower the `BUFFERING` threshold from 15s to 8s, lower the `UNSTARTED` threshold from 10s to 4s, and shorten the recovery cooldown. The tradeoff is a higher chance of recovery being triggered during short, normal buffering hiccups.

Question:
Do I want faster recovery, even if it occasionally causes false-positive restarts during short, normal buffering?