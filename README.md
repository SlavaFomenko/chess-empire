# Chess Empire

## Chess what?

Chess Empire is a nice place for those who love playing chess. Created by two friends - reactAndChessLover (Fomenko Vyacheslav)
and quickLoseMaster (Rehenel Timothy), this project has become for both an important step in their growth as professional developers.
And they hope (and trying their best) for this project to grow and be better and better as well (especially after huge clean-up and refactoring (coming soon)).
Anyway, let's move on

## How can i run this? (and can i?...)
First of all, you need Docker installed on your machine. If you have it - you can launch `local_reacreate_docker.sh` script.
This will (re)build all the project containers.

Also, you may need to add `chess-empire.ua` as the localhost alias in hosts file in your OS.

To run game server, go inside the api container and type `php s.php` for running s.php file responsible for the game server.

Next, via DataGrip or other way, execute all queries from `./create_database.sql` to create all the tables in the DB.
Connection string used in DataGrip: `jdbc:mysql://localhost:13306`

Now you can go to `chess-empire.ua` and play chess! (at least we hope so)

## Project structure

- `api/` - backend root directory
- `frontend/` - frontend root directory
- `docker/` - Dockerfiles, certs, configs etc
- `.env` - environment variables
- `create_database.sql` - queries for creating the tables in DB
- `docker-compose*.yaml` - docker-compose files with containers configuration
- `local_reacreate_docker.sh` - shell script for rebuild


# usePatterns

Speaking about patterns, there are at least 6 of them. Let's look at each one closely.

## Chain of Responsibility (Timothy Rehenel)
When the backend receives a request from the client, there's a bunch of stuff to do: load all configurations, initialize the database
connection, parse the request, route it to the corresponding controller, etc. The bigger the project gets, the harder is to keep all these processes
organized.

To solve this problem, I've applied a Chain of Responsibility pattern. In `handy/` (framework's directory) we can see the `Handling` directory, in
which we can find almost all related to this pattern files. Handler - an interface, in which I've declared all methods, required from the handlers.
AbstractHandler - basic handler with all boilerplate implemented in it. The rest - specific handlers for every process. F.e., 
OrmHandler will initialize the ORM, ConfigParserHandler will execute the config parser (no way), etc.

Now we can [declare](./api/handy/Core.php#L25-L32) the list of our handlers in the correct order, [chain](./api/handy/Core.php#L34-L37)
them up, and [run](./api/handy/Core.php#L46) first of them. After finishing the logic, it'll launch the next handler in the chain.

## State (Timothy Rehenel)
You know what the event listener is (right?). Maybe you even have an experience with something like socket.io. If so, then
you know that there's an event triggered by the server|client on the opposite side, and there's the list of listeners for this event called when the event is received.
For the player's socket connection. We need some listeners only in the specific period of time (when the player is in some specific STATE :) ).
The idea of removing and adding all the listeners manually is not the best you and I will have today, probably. So...

Let's take a look at the event lifecycle in the Handy`s sockets system:

Receiving the event
->
Calling Server's listeners
->
Calling Room's listeners
->
Calling Client's listeners

To deal with our current problem, lets add one more step before calling client's listeners: calling State's listeners.
Basically, the state is just a container for the group of the listeners. Using them, we can declare all the listeners in 
corresponding states, and then just apply the state for the client. (For example, [this one](./api/src/Socket/States/UnauthorizedState.php))

The same idea is applied on the frontend, using not classes, but objects in which the keys - the names of events,
and the values are the listeners of these events. They're stored [here](./frontend/src/shared/socket/states).

Technically, this is an implementation of the State pattern, but with event listeners instead of plain method calls.

## Memento (Timothy Rehenel)
One more problem. Let’s imagine that our API got an entity needed for logic from the database.
Then, the API made some changes to this entity. How can we save these changes in the database? Plain query is also a solution,
but what about the cases where multiple different entities are involved? Seems like wee need to track all changes in these entities,
and then analyze them and prepare all the queries automatically...

And that's what the [EntityManager](./api/handy/ORM/EntityManager.php) is made for. First of all, this dude got its own
[array of entities states](./api/handy/ORM/EntityManager.php#L23). Every time API asking the Database for some entity, EntityManager
will create an item in this list for the entity received. Each of these items have two important keys: `before` and `after`
(Quite obvious which one stand for what). The values for these keys are either special or normal states of entities.
[Special states](./api/handy/ORM/EntityManager.php#L16-L17) are made for special states (no way) line when entity is not persisted yet,
must be deleted or doesn't have `before` state because was created by the API.
Normal states are just reduced snapshots of entity produced by [corresponding method](./api/handy/ORM/BaseEntity.php#L32-L45).
After performing all the necessary operations, the API can call the EntityManager flush() method. In this method, EntityManager
walks through its states list, comparing for each item its `before` and `after` states, and preparing INSERT, UPDATE or DELETE query
depend on the differences between these two states.


# usePrinciples

There are also a bunch (at least 5) of programming principles used. Let's give them a quick overview.

## S in SOLID - Single Responsibility
Especially on the backend, we have tried as much as possible to limit all entities to one task and one purpose (without going to extremes).
For example, during the development process, we've noticed that the [Router](./api/handy/Routing/Router.php) gets too complicated because of
the process of searching required attributes among the group of the files. What's more, this logic could be used again in other parts,
like Console Commands, ORM etc. So we decided to move out this logic into the [Resolver class](./api/handy/Utils/Resolver.php).
That way, every part is responsible for only one kind of functionality: Router for routing, ORM for work with database,
Resolver - for resolving all the properties, attributes, files and other by some criteria. (Is it a DRY principle appliance too?)

## O in SOLID - Open/Closed
Some Handy functionality requires developers to inherit their classes from Handy's classes (for example, all Entities must be
inherited from the [BaseEntity](./api/handy/ORM/BaseEntity.php)). To prevent inconvenience, errors, and bad project structure,
we've made all these Base-like classes opened for extension by adding custom logic required for the projects, and closed
for changing already defined behaviors (also we've tried to exclude any need for this).

## L in SOLID - Liscov Substitution
This one is somehow related with previous. As mentioned earlier, some Handy functionality requires developers to inherit
their classes from Handy's classes. And it's pretty important the whole system works well with custom, extended classes being used.
For example, Handy's Socket system is designed so that developers can easily inherit their classes from the base classes
while the system continues to work normally. This is due to the fact that all child classes ([example](./api/src/Socket))
can be used instead of [parent classes](./api/handy/Socket).

## KISS
One of good examples of this principle appliance is the Sockets event system. Instead of creating unclear structure with
lots of classes and complex solutions, there's just an [IEventFlow interface](./api/handy/Socket/IEventFlow.php).
Every class that implements this interface can be used in Event flow (which also allows you to customize this flow as we
did by integrating the client states, as described in usePatterns chapter). And to add a new listener to the event, you
only need to call on() method for EventFlow-implementing object, passing event name and callback function as an arguments.
So, we've Kept It Stupid (or Smart?) Simple.

## FAIL FAST
Almost in every directory of Handy you can find the Exception folder. We've tried to prevent as much unwanted problems with
code as possible by throwing corresponding Exception ASAP when something goes wrong. By the way, we're planning to add
an exception controller to Handy framework for nice exceptions callstack displaying in the browser.

## NotYAGNI or SOMNI
This principle was formulated by us. While developing your concrete project, YAGNI (You Aren't Gonna Need It) principle
is pretty helpful and can save your time and work by minimising unnecessary parts to develop. But for developing the 
framework (which is a part of our project since it'll be presented as our coursework too), we think, it's quite opposite.
Framework is literally as many instruments as someone may need to build their stuff. So we've come up with our own principle:
SOMNI (SOmebody May Need It), which implies that you develop diverse enough set of tools (while keeping your working process
optimised, of course).


# useRefactoring (a little bit)
In general, we've used such basic refactoring techniques as extracting methods, inverting ifs, replacing subclass with fields ets.
Also, we're using Code Analysis functionality built in PhpStorm. It's powerful and help to detect and refactor very wide spectre of
problems.

# Thank You for Your attention!