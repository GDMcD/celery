-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2019 at 03:46 AM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mcdougall-isu`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

DROP TABLE IF EXISTS `inventories`;
CREATE TABLE `inventories` (
  `user_id` int(11) NOT NULL,
  `1` varchar(6) NOT NULL DEFAULT '2,2,0',
  `2` varchar(6) NOT NULL DEFAULT '1,0,0',
  `3` varchar(6) NOT NULL DEFAULT 'x,x,0',
  `4` varchar(6) NOT NULL DEFAULT '1,3,0',
  `5` varchar(6) NOT NULL DEFAULT 'x,x,0',
  `6` varchar(6) NOT NULL DEFAULT '0,4,0',
  `7` varchar(6) NOT NULL DEFAULT '3,4,0',
  `8` varchar(6) NOT NULL DEFAULT 'x,x,0',
  `9` varchar(6) NOT NULL DEFAULT 'x,x,0',
  `10` varchar(6) NOT NULL DEFAULT 'x,x,0',
  `11` varchar(6) NOT NULL DEFAULT '4,2,0',
  `12` varchar(6) NOT NULL DEFAULT '3,3,0',
  `13` varchar(6) NOT NULL DEFAULT 'x,x,0',
  `14` varchar(5) NOT NULL DEFAULT 'x,x,0',
  `15` varchar(5) NOT NULL DEFAULT 'x,x,0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inventories`
--

INSERT INTO `inventories` (`user_id`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`) VALUES
(1, '2,2,0', '1,0,0', 'x,x,0', '1,3,0', 'x,x,0', '0,4,0', '3,4,0', 'x,x,0', 'x,x,0', 'x,x,0', '4,2,0', '3,3,0', 'x,x,0', 'x,x,0', 'x,x,0'),
(2, '2,2,0', '1,0,0', 'x,x,0', '1,3,0', 'x,x,0', '0,4,0', '3,4,0', 'x,x,0', 'x,x,0', 'x,x,0', '4,2,0', '3,3,0', 'x,x,0', 'x,x,0', 'x,x,0'),
(3, '2,2,0', '1,0,0', 'x,x,0', '1,3,0', 'x,x,0', '0,4,0', '3,4,0', 'x,x,0', 'x,x,0', 'x,x,0', '4,2,0', '3,3,0', 'x,x,0', 'x,x,0', 'x,x,0'),
(4, '2,2,1', '1,0,0', 'x,x,0', '1,3,0', 'x,x,0', '0,4,0', '3,4,0', 'x,x,0', 'x,x,0', 'x,x,0', '4,2,0', '3,3,0', 'x,x,0', 'x,x,0', 'x,x,0'),
(5, '2,2,0', '1,0,0', 'x,x,0', '1,3,0', 'x,x,0', '0,4,0', '3,4,0', 'x,x,0', 'x,x,0', 'x,x,0', '4,2,0', '3,3,0', 'x,x,0', 'x,x,0', 'x,x,0'),
(6, '2,2,0', '1,0,0', 'x,x,0', '1,3,0', 'x,x,0', '0,4,0', '3,4,0', 'x,x,0', 'x,x,0', 'x,x,0', '4,2,0', '3,3,0', 'x,x,0', 'x,x,0', 'x,x,0'),
(7, '2,2,3', '1,0,0', 'x,x,0', '1,3,0', 'x,x,0', '0,4,0', '3,4,0', 'x,x,0', 'x,x,0', 'x,x,0', '4,2,0', '3,3,0', 'x,x,0', 'x,x,0', 'x,x,0'),
(8, '2,2,0', '1,0,0', 'x,x,0', '1,3,0', 'x,x,0', '0,4,0', '3,4,0', 'x,x,0', 'x,x,0', 'x,x,0', '4,2,0', '3,3,0', 'x,x,0', 'x,x,0', 'x,x,0'),
(9, 'x,x,2', 'x,x,0', '1,1,1', '1,3,1', '0,0,1', '0,4,3', '3,4,0', 'x,x,0', '4,0,1', '4,1,1', '4,2,1', '3,3,1', '1,1,2', 'x,x,1', 'x,x,1');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `item_id` tinyint(2) NOT NULL,
  `item` varchar(25) NOT NULL,
  `description` varchar(50) NOT NULL,
  `image` varchar(500) NOT NULL,
  `max` tinyint(1) NOT NULL DEFAULT '1',
  `lock_id` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item`, `description`, `image`, `max`, `lock_id`) VALUES
(1, 'A Coconut', 'Quite the tasty treat.', 'images/coconut.png', 3, 0),
(2, 'A Slippery Fish', 'A meal for someone...', 'images/fish.png', 1, 0),
(3, 'A Snorkel', 'Don\'t go swimming without it!', 'images/snorkel.png', 1, 0),
(4, 'A Large Fishing Rod', 'Hook some big ones!', 'images/rod.png', 1, 0),
(5, 'An Old Mast', 'Surprisingly sturdy...', 'images/mast.png', 1, 0),
(6, 'A Wooden Plank', 'This will come in handy.', 'images/plank.png', 3, 0),
(7, 'A Heavy Treasure Chest', 'The weight hurts your back.', 'images/chest.png', 1, 10),
(8, 'A Canvas Sail', 'Seems to be in good condition.', 'images/sail.png', 1, 0),
(9, 'A Golden Compass', 'Never get lost again!', 'images/compass.png', 1, 0),
(10, 'A Dusty Treasure Map', 'I wonder where it leads?', 'images/map.png', 1, 0),
(11, 'A Dirty Key', 'This must open something, right?', 'images/key.png', 1, 14),
(12, 'A Shiny Sword', 'Be careful not to cut yourself!', 'images/sword.png', 1, 15),
(13, 'A Jar of Sticky Resin', 'Adhesive and 100% waterproof.', 'images/resin.png', 2, 0),
(14, 'Jaguar Beaten?', '', '', 1, 0),
(15, 'Snake Beaten?', '', '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
  `location_id` tinyint(2) NOT NULL,
  `x` tinyint(1) NOT NULL,
  `y` tinyint(1) NOT NULL,
  `location` varchar(35) NOT NULL,
  `description` varchar(650) NOT NULL,
  `north` int(1) NOT NULL,
  `east` int(1) NOT NULL,
  `south` int(1) NOT NULL,
  `west` int(1) NOT NULL,
  `random` tinyint(1) NOT NULL DEFAULT '0',
  `audio` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `x`, `y`, `location`, `description`, `north`, `east`, `south`, `west`, `random`, `audio`) VALUES
(1, 0, 0, 'An Old Shipwreck', 'You\'re swimming on a shallow coral reef. It is shallow enough that in some spots, you can stand.<br><br>To the north and the west lies the ocean, deep and dark.<br>To the south is the island, where you can see a waterfall tumbling into the ocean.<br>To the east is a sandy delta, where a river flows out into the ocean.<br><br>In the water below you is an old shipwreck. Slime and weeds cover most of the wood, as tropical fish swim in and out of little holes.<br>Some of the ship is surprisingly intact.', 0, 1, 3, 0, 0, 'audio/ocean.mp3'),
(2, 1, 0, 'A River Mouth', 'The river flows out into the ocean here, which stretches to the north as far as the eye can see.<br>To the east is a jungle, thick and dense. You have a feeling you\'ll get lost in there.<br>To the south, the river, flowing past you. Cliffs and waterfalls make it impossible to pass in that direction.<br>The ocean lies also to the west, but there is a shallow bank below the water.<br><br>There seems to be a dark shape under the water there... maybe a shark, but it isn\'t moving. It could be worth checking out.', 0, 2, 0, 1, 0, 'audio/ocean.mp3'),
(3, 3, 0, 'A Jungle', 'Somewhere in the jungle. It\'s hard to find your way around in here, with all the tall trees and hanging vines.<br>You hear a rustling in the bush behind you. The humid air is getting to you.<br><br>You hear the roar of waves through the trees. The ocean must be near.', 0, 1, 2, 2, 1, 'audio/jungle.mp3'),
(4, 4, 0, 'A Barren Rock', 'You are standing on a rocky outcropping, looking out at the ocean to the north and east.<br>To the south and west lies the jungle, thick and dense. You have a feeling you\'ll get lost in there.<br><br>The ground is full of little cracks and holes, perfect for hiding things.', 0, 0, 1, 1, 0, 'audio/ocean.mp3'),
(5, 1, 1, 'An Abandoned Fishing Hut', 'You\'re standing beside the river. It flows past you to the north, where you can see it flow into the ocean in the distance.<br>To the south and west, cliffs and waterfalls along the river banks make it impossible to pass.<br>To the east is the jungle, thick and dense. You have a feeling you\'d get lost in there.<br><br>An old fishing hut sits beside the river. Its thatched roof has a hole in it, and the door lies to the side in the bushes.<br>Maybe there is something useful inside?', 1, 1, 0, 0, -1, 'audio/river.mp3'),
(6, 2, 1, 'A Jungle', 'Somewhere in the jungle. It\'s hard to find your way around in here, with all the tall trees and hanging vines.<br>You hear a rustling in the bush behind you. The humid air is getting to you.', 0, 2, 1, 1, 1, 'audio/jungle.mp3'),
(7, 4, 1, 'A Jungle Cave', 'Somewhere in the jungle. It\'s hard to find your way around in here, with all the tall trees and hanging vines.<br>You hear a rustling in the bush behind you. The humid air is getting to you.<br><br>You hear the roar of waves through the trees. The ocean must be near.<br><br>A dark-mouthed cave opening lies beside the path ahead.', 1, 0, 1, 2, 1, 'audio/jungle.mp3'),
(8, 2, 2, 'A Lonely Palm', 'A single palm tree lives in the sand here. It casts shade on you, shielding you from the scorching sun.<br><br>To the north and east lies a jungle, thick and dense. You have a feeling you\'d get lost in there.<br>To the south, a river flows into the ground, seeming to disappear.<br>To the west, you can also see a river, but a field of boulders and scraggy brush make the way impassable.', 1, 1, 1, 0, 0, 'audio/desert.mp3'),
(9, 3, 2, 'A Jungle', 'Somewhere in the jungle. It\'s hard to find your way around in here, with all the tall trees and hanging vines.<br>You hear a rustling in the bush behind you. The humid air is getting to you.', 2, 1, 1, 1, 1, 'audio/jungle.mp3'),
(10, 4, 2, 'A Jaguar\'s Den', 'Somewhere in the jungle. It\'s hard to find your way around in here, with all the tall trees and hanging vines.<br>You hear a rustling in the bush behind you. The humid air is getting to you.<br><br>You hear the roar of waves through the trees. The ocean must be near.', 1, 0, 2, 1, 1, 'audio/jungle.mp3'),
(11, 0, 3, 'A Silver Waterfall', 'The river here tumbles westward over a small cliff into the ocean below. The mist here is very refreshing, and the water looks very clear.<br><br>To the south, a slope leads down to the water, where a small island lies. It looks shallow enough to walk across.<br>To the east, the river flows past you. Rocky ledges make it impossible to travel upstream.<br>To the north stretches a long beach, eventually disappearing into the water, where a shallow bank seems to sit under the surface. There is a dark shape under the water there, large enough to be a shark, but not moving. It could be worth checking out.', 3, 0, 1, 0, 2, 'audio/waterfall.mp3'),
(12, 1, 3, 'A Rushing River', 'You are standing on the bank of a quick-flowing river. The river flows past you from the south, where a series of small cliffs make it impossible to travel upstream.<br><br>Where you stand, the river splits into three.<br>One arm, the biggest, continues in the northerly direction.<br>The other two, slightly smaller, travel eastward and westward respectively.<br><br>Both smaller branches seem to disappear in the distance; the western arm down a waterfall, the eastern into the ground.<br><br>Some stepping stones make it possible to cross the river here.', 2, 1, 0, 1, -1, 'audio/river.mp3'),
(13, 2, 3, 'A Vanishing River', 'A strange phenomenon occurs here; the river seems to disappear into the ground. It flows in from the west, where a small cliff makes the way impassable.<br><br>To the north, you see a palm tree standing alone in the sand.<br>To the east, more sand.<br>And to the south, sand covered in boulders, which you don\'t think you could climb around.<br><br>Indeed, there must be some sort of underground cave system through which the river flows.<br><br>I wonder what would happen if you jumped in?', 1, 1, 0, 0, 2, 'audio/river.mp3'),
(14, 3, 3, 'A Snake\'s Nest', 'You find yourself on sandy ground, with not many distinguishable features.<br><br>To the north is a jungle, thick and dense. You have a feeling you\'d get lost in there.<br>To the south is a sandy beach, stretching along the oceanfront.<br>To the west, you see a river, vanishing into the sand, and to the east is sand, again. There is nothing worth seeing over there.<br><br>In front of you is a snake. You\'re not sure what species it is, but it looks venomous. It protects a group of hatchlings in its nest.<br><br>The snake hisses at you.', 1, 0, 1, 1, 0, 'audio/snake.mp3'),
(15, 0, 4, 'A Small Island', 'You are standing on a small island...<br>It\'s more of a sand bank, really.<br><br>To the west and the south, the ocean shimmers as far as the eye can see.<br><br>Across the shallow water to the north and east, you can see the main island, where the land slants upward away from the beaches.<br>There is a waterfall to the north.', 1, 1, 0, 0, 0, 'audio/ocean.mp3'),
(16, 1, 4, 'A Burbling Spring', 'In front of you is a spring, whose water is crystal clear. A stream flows out from the spring to the north, where you can see it widening into a river.<br><br>To the west is a small island. The water there looks shallow enough to wade across.<br>To the east is a long beach, slanting steeply towards the ocean, which stretches to the south and fills the horizon.', 1, 2, 0, 1, -1, 'audio/bubbles.mp3'),
(17, 3, 4, 'A Sandy Beach', 'There is not much to see here.<br><br>The beach stretches to the east and west. In the east, you see a spring, while in the west, the sand disappears under very shallow water.<br>To the north of you is more sand, while to the south, the sand slopes down into the ocean.<br><br>The shallow water to the east looks like a perfect place to build and launch your raft.', 1, 1, 0, 2, 0, 'audio/ocean.mp3'),
(18, 4, 4, 'A Shallow Coral Reef', 'You are standing in the water, which comes about halfway up your thighs. The bottom is sandy, but there are some corals scattered here and there. You are careful to avoid stepping on them, and notice that there is a reef not too far out.<br><br>To the north is a jungle, thick and dense. You have a feeling you\'d get lost in there.<br>Looking to the west you see a long, sandy beach.<br>To the east and south lies the Pacific ocean, dark blue.<br><br>You remark that the calm waters created by the reef are an ideal spot to launch your raft, and the shallow water you are standing in would facilitate the creation of said raft.', 2, 1, 0, 1, 0, 'audio/ocean.mp3'),
(19, 5, 4, 'An Open Ocean', 'You are floating across the open ocean.<br><br>The strong wind seems to be taking you in the right direction as Rawaki grows smaller and smaller behind you.<br><br>There\'s not much you can do, except hope. At the speed you\'re moving, you estimate that you\'ll arrive at Kanton Island in less than twelve hours.<br><br><br><b>There is no turning back now.</b>', 0, 0, 0, 0, 0, 'audio/open-ocean.mp3'),
(20, 5, 5, '<span id = \'dead\'>GAME OVER</span>', '<span id = \'restart\'>Type anything to restart<br>or<br><a href = \'logout.php\'>logout</a></span>', 0, 0, 0, 0, 0, 'audio/game-over.mp3');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` bigint(11) NOT NULL,
  `first` varchar(30) NOT NULL,
  `last` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `birth` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `ACTIVE` tinyint(1) NOT NULL DEFAULT '1',
  `x` tinyint(1) NOT NULL DEFAULT '2',
  `y` tinyint(1) NOT NULL DEFAULT '2',
  `health` tinyint(1) NOT NULL DEFAULT '3',
  `HPGained` tinyint(1) NOT NULL DEFAULT '0',
  `Raft` varchar(7) NOT NULL DEFAULT '0,0,0,0',
  `win` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first`, `last`, `email`, `birth`, `password`, `ACTIVE`, `x`, `y`, `health`, `HPGained`, `Raft`, `win`) VALUES
(1, 'Graeme', 'McDougall', 'mcdougall.graeme13@gmail.com', '2002-08-28', '$2y$10$lbPDkmX5CD/YpE0tSjrGQeccA1Ef5TghkCPWJQ63VOKPTniqzaE..', 1, 2, 2, 3, 0, '0,0,0,0', 0),
(2, 'Person', 'McWerson', 'email@valid.ca', '1994-07-07', '$2y$10$0NnxDnKGl0Hces5bJVSRpegHn4OC0jFctYXs8lb5nVNfkDpONdnle', 1, 2, 2, 3, 0, '0,0,0,0', 0),
(3, 'ABC', 'DEF', 'abcdef@alphabet.uk', '1999-09-09', '$2y$10$l.jq7X6D3SNWbwdOYv3fteRpMeCl/9Hcif9s3tTvCaT9KQb911PxC', 0, 2, 2, 3, 0, '0,0,0,0', 0),
(4, 'Duncan', 'McDougall', 'mcdougallduncan06@gmail.com', '2002-08-28', '$2y$10$FLrc4ka7SBUAxn0SQGImvOPe7cdkHYK8MZsqpGZ7aVKQo8sDx/XSO', 1, 2, 2, 3, 0, '0,0,0,0', 0),
(5, 'Duncan', 'buddy', 'hi@hi.com', '2011-01-03', '$2y$10$sHW7PJQiDIakL1y5MJW0bO9un19ULeS4qpz4BdbiBAQTWxvLmGIOC', 1, 1, 4, 4, 3, '0,0,0,0', 0),
(6, 'Graeme', 'McDouglas', 'sfhsdlkfjg@gmail.com', '1939-01-01', '$2y$10$dXrORFsgeXB4AVFTFC7Cbe0RHSguan/BVBTKrAJFweveCElCaO97i', 1, 3, 4, 1, 0, '0,0,0,0', 0),
(7, 'sandra', 'hyshka', 'hyshka.sandra@gmail.com', '1969-09-10', '$2y$10$wn.NAKb2lZIABjDRIApBH.aYKpkKvafKAt1bNMtTlDOU6BYmHN2Mm', 1, 4, 4, 3, 0, '0,0,0,0', 0),
(8, 'Hey', 'Keb', 'gmcd@email.com', '2002-08-05', '$2y$10$vmZriplu8JAjnF4MW8sQ0efPI8PXf/AHb1u31hVTdGBe3k3k1Nkw.', 1, 2, 2, 3, 0, '0,0,0,0', 0),
(9, 'Chile', 'Wile', 'chilewilechilewile@gmail.com', '2002-08-28', '$2y$10$S3hJbWlNnpQigvONdZmjoO96JrcAj3XaLqjJ88plMGF69qJ3zlU4u', 1, 4, 4, 2, 1, '0,0,0,0', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
