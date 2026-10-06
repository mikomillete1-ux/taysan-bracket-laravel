# Barangay Taysan — Game Scheduling & Automated Bracketing System (Laravel module)

Drop-in module implementing the capstone's core feature: registering teams,
auto-generating a single-elimination bracket, scheduling matches without
venue conflicts, and recording results that automatically advance winners.

## What's in here

```
app/Models/Team.php
app/Models/Bracket.php
app/Models/GameMatch.php          <- table name is "matches"; class avoids PHP's reserved word
app/Services/BracketGeneratorService.php   <- if/else bracket-building logic
app/Services/ScheduleService.php           <- if/else venue conflict checking
app/Http/Controllers/TeamController.php
app/Http/Controllers/BracketController.php
app/Http/Controllers/MatchController.php
database/migrations/*.php
resources/views/layouts/app.blade.php      <- header with barangay logo
resources/views/teams/index.blade.php
resources/views/brackets/create.blade.php
resources/views/brackets/show.blade.php    <- the bracket / schedule / results table
routes/web.php
public/images/taysan-logo-placeholder.svg
```

## Installation into your existing Laravel project

1. Copy the `app/`, `database/`, `resources/`, `routes/`, and `public/`
   folders into your project, merging with what's already there
   (don't overwrite your existing `routes/web.php` — paste its contents
   into yours instead).
2. Run the migrations:
   ```
   php artisan migrate
   ```
3. If you want uploaded team logos to be viewable, link storage:
   ```
   php artisan storage:link
   ```
4. Visit `/teams` to register teams, then `/brackets/create` to generate
   a bracket.

## About the logo

I could not find an official published seal for Barangay Taysan, Legazpi
City, Albay to use, so `public/images/taysan-logo-placeholder.svg` is a
generic placeholder badge, not the real seal. Replace that file with the
actual logo image once you get it from the barangay office (keep the
same filename, or update the `asset('images/...')` path in
`resources/views/layouts/app.blade.php`).

## Where the if/else logic lives (for your documentation/defense)

- **`BracketGeneratorService::generateSingleElimination()`** — uses a
  chain of `if / elseif / else` to pick the bracket size (2, 4, 8, 16...)
  and round count from the number of registered teams, then more
  `if / elseif / else` branches to decide, slot by slot, whether a match
  is a normal pairing, a bye (one team, auto-advance), or an empty slot.
- **`BracketGeneratorService::advanceWinner()`** — an `if/else` decides
  whether the winner fills the "team1" or "team2" slot of the next round
  based on whether the finished match number is odd or even.
- **`ScheduleService::isVenueAvailable()`** — an `if/else` compares the
  requested time range against every existing booking at that venue to
  detect overlaps before a match can be scheduled.
- **`MatchController::recordResult()`** — `if/elseif/else` decides the
  winner from the two scores, or flags a tie.
- **`GameMatch::getStatusLabelAttribute()`** — `if/elseif/else` chain
  turning the internal status code into a readable label for the view.

## Notes / things you may want to extend for the full capstone

- Add authentication/admin middleware around team and bracket management
  so only barangay staff can register teams or record results.
- Add a round-robin generator alongside the single-elimination one if
  your capstone scope calls for both formats.
- Add SMS/email notifications when a match is scheduled (your earlier
  election system project already used semaphore/SMS — the same
  approach would work here).
