# ClickMeeting Recruitment Test

## Getting Started

1. Have Docker / Docker Desktop installed on local computer
2. Run `make build` to build fresh images
3. Run `make start` to create and spin up the app
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `make down` to stop the Docker containers.

Check out Makefile to see more commands.

## The Task

### Handover

1. Clone this repo and push it to a new private repository on Github
2. Do each task by creating a separate pull request on Github (like you'd do in a real-life project)
3. Give access to the repo to people mentioned by the recruitment team
4. It would be nice if you leave the repository private after the recruitment process, but since it's your code and repository – it's up to you ;-)

### Tasks

1. Right now number of participants who can attend a given meeting is unlimited. Limit the number of participants in a given meeting to 5. 
2. Meeting endpoint should display status of the meeting:
- "open to registration" - if meeting has fewer than 5 participants and didn't start yet 
- "full" - if there are 5 participants, but it didn't start yet
- "in session" - if it has started but didn't finish
- "done" – when the meeting is finished
3. Implement a simple rating system – when the meeting is done (right now every meeting lasts for an hour) every participant can rate the meeting from 1 to 5. The rating may be cast through an endpoint, e.g. `POST /meetings/{id}/rate`. Every participant can rate every meeting they participated once.
4. (if you have spare time) Create an endpoint with a list of the meetings. List should display name, start time and average rating of the meetings (null if meeting was not rated by anyone). The list can be ordered by the date (ascending and descending) and filtered by the status of the meeting. Prepare the solution to be easily extensible by other ordering and filtering rules.

### What do we look at

1. Architectural design of the solution (dependency direction, encapsulation, granularity)
2. Tests (if classes are easily testable, if unit tests are really unit, if feature tests are representing business rules)
3. Intuitive use of REST verbs

The task is meant to be done in few hours, so don't "gold plate" it. If you'd like to improve something in your solution, but it would require more time – write about it.