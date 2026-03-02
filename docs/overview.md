Auction Grid Battle Game
Project Overview

The Auction Grid Battle Game is a strategic multiplayer web application that combines auction mechanics, deck building, and automated battle simulation. Teams compete by bidding on characters in an auction system and then deploying those characters on a 10 × 10 grid battlefield. Once the characters are placed, an automated simulation determines the outcome of the battle based on character attributes, positioning, and abilities.

The objective of the game is to strategically acquire powerful characters during the auction phase and deploy them effectively during the battle phase to defeat opposing teams.

This project is designed to demonstrate concepts such as real-time auctions, strategic resource management, grid-based simulation, and multiplayer competition.

Core Game Flow

The game progresses through the following phases:

Team Registration

Character Creation by Moderator

Character Auction

Deck Finalization

Grid Deployment

Battle Simulation

Leaderboard and Results

Each phase introduces different strategic elements that influence the final outcome of the game.

User Roles

The system includes two main user roles.

1. Moderator (Game Host)

The moderator manages the overall game environment.

Responsibilities include:

Creating and managing the tournament

Adding characters to the character pool

Starting and controlling auctions

Monitoring team bids

Initiating battle simulations

Viewing results and leaderboards

2. Teams (Players)

Teams are the participants in the game. Each team competes to acquire characters during the auction phase and deploy them strategically during battles.

Team responsibilities include:

Registering in the system

Participating in character auctions

Managing their character roster

Deploying characters on the battlefield

Competing in battles against other teams

Each team receives a fixed amount of starting coins that they use to bid during auctions.

Game Mechanics
Auction System

Characters are auctioned one at a time. Teams place bids using their available coins.

Auction rules include:

Each character has a base price.

Teams must bid above the current highest bid.

The auction runs for a limited time.

The highest bidder at the end of the timer wins the character.

Once a character is won:

The bid amount is deducted from the team’s coin balance.

The character is added to the team's roster.

Deck Building

After the auction phase ends, teams finalize their character decks.

Rules:

Each team can deploy a limited number of characters.

Characters must be selected from those won in the auction.

Decks are locked before the battle begins.

Strategic selection of characters plays a major role in the outcome of battles.

Grid-Based Battlefield

Battles take place on a 10 × 10 grid battlefield.

The grid contains 100 cells.

Each character occupies one grid cell.

Teams place characters in designated deployment zones.

Example deployment zones:

Team A deploys in the top rows.

Team B deploys in the bottom rows.

The middle rows act as a neutral combat area.

Battle Simulation

Once deployment is complete, the battle begins automatically.

The simulation engine controls:

Character movement

Combat interactions

Ability activation

Health and damage calculations

Characters move toward enemies, attack when in range, and use their abilities based on predefined rules.

The battle ends when all characters of one team are defeated.

Victory Conditions

A team wins when:

All opposing characters are eliminated.

If both teams still have surviving characters after a time limit, the team with the greater number of remaining units is declared the winner.

Application Pages

The application consists of several key pages that support the full game flow.

1. Landing Page

The landing page provides an introduction to the game and allows users to access login or registration options.

Features:

Game description

Login button

Register button

2. Registration Page

Allows new users to create accounts as teams.

Features:

Team name input

Email and password fields

Account creation

3. Login Page

Allows registered users and moderators to access the system.

Features:

Email login

Password authentication

4. Team Dashboard

The main interface for teams to manage their activities.

Features:

Team coin balance

Owned characters

Auction participation

Battle history

Deck management

5. Moderator Dashboard

Provides administrative controls for managing the game.

Features:

Create characters

Start auctions

Monitor bids

Start battles

Manage leaderboard

6. Character Management Page

Allows the moderator to create and manage characters available in the auction.

Features:

Add new characters

Define character stats

Set base auction price

7. Auction Page

Displays the current character being auctioned and allows teams to place bids.

Features:

Current bid display

Bid submission

Auction countdown timer

Winning team display

8. Deck Management Page

Allows teams to view and manage the characters they acquired.

Features:

Character roster

Deck selection

Character stats display

9. Grid Deployment Page

Allows teams to place characters on the battlefield grid before the battle begins.

Features:

10 × 10 grid interface

Drag-and-drop character placement

Deployment zone validation

10. Battle Simulation Page

Displays the automated battle simulation.

Features:

Grid battlefield visualization

Character movement

Combat interactions

Real-time battle updates

11. Leaderboard Page

Displays team rankings based on battle results.

Features:

Team rankings

Total wins and losses

Points table

Technology Stack

The system will be developed using modern web technologies.

Backend:

Laravel (PHP Framework)

Frontend:

HTML

CSS

JavaScript

Database:

MySQL

Additional technologies may include real-time communication systems for auctions and battle updates.

Conclusion

The Auction Grid Battle Game combines strategy, competition, and automated simulation to create an engaging multiplayer experience. By integrating auction mechanics with tactical grid-based combat, the game encourages players to carefully manage their resources and strategically position their characters to achieve victory.