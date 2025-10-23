<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestScript extends Command
{
    protected $signature = 'database:create';
    protected $description = 'This command will create and populate the database';

    public function handle()
    {
        DB::statement('PRAGMA foreign_keys = OFF;');
        DB::statement("DROP TABLE IF EXISTS user;");
        DB::statement("DROP TABLE IF EXISTS professional;");
        DB::statement("DROP TABLE IF EXISTS project_user;");
        DB::statement("DROP TABLE IF EXISTS project_professional;");
        DB::statement("DROP TABLE IF EXISTS project;");
        DB::statement("DROP TABLE IF EXISTS project_observation;");
        DB::statement("DROP TABLE IF EXISTS observation;");
        DB::statement("DROP TABLE IF EXISTS media;");
        DB::statement("DROP TABLE IF EXISTS location;");
        DB::statement("DROP TABLE IF EXISTS groupChat_user;");
        DB::statement("DROP TABLE IF EXISTS groupChat;");
        DB::statement("DROP TABLE IF EXISTS message;");
        DB::statement("DROP TABLE IF EXISTS species;");
        DB::statement("DROP TABLE IF EXISTS genus;");
        DB::statement("DROP TABLE IF EXISTS `order`;");
        DB::statement("DROP TABLE IF EXISTS family;");
        DB::statement("DROP TABLE IF EXISTS class;");
        DB::statement("DROP TABLE IF EXISTS phylum;");

        DB::unprepared("
              CREATE TABLE IF NOT EXISTS sessions (
                id VARCHAR(255) PRIMARY KEY,
                user_id INTEGER NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                payload TEXT NOT NULL,
                last_activity INTEGER NOT NULL
            );

            CREATE TABLE user (
            email VARCHAR(255) PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL
            );

            CREATE TABLE professional (
            email VARCHAR(255) PRIMARY KEY,
            degree VARCHAR(255),
            certification VARCHAR(255),
            specialization VARCHAR(255),
            FOREIGN KEY (email) REFERENCES user(email) ON DELETE CASCADE
            );

            CREATE TABLE project (
            projectID INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255),
            description TEXT
            );

            CREATE TABLE project_user (
            projectID INTEGER,
            email VARCHAR(255),
            PRIMARY KEY (email, projectID),
            FOREIGN KEY (projectID) REFERENCES project(projectID) ON DELETE CASCADE,
            FOREIGN KEY (email) REFERENCES user(email) ON DELETE CASCADE
            );

            CREATE TABLE project_professional (
            projectID INTEGER,
            email VARCHAR(255),
            PRIMARY KEY (email, projectID),
            FOREIGN KEY (projectID) REFERENCES project(projectID) ON DELETE CASCADE,
            FOREIGN KEY (email) REFERENCES professional(email) ON DELETE CASCADE
            );

            CREATE TABLE project_observation (
            projectID INTEGER,
            observationID INTEGER,
            PRIMARY KEY (observationID, projectID),
            FOREIGN KEY (projectID) REFERENCES project(projectID) ON DELETE CASCADE,
            FOREIGN KEY (observationID) REFERENCES observation(observationID) ON DELETE CASCADE
            );

            CREATE TABLE observation(
            observationID INTEGER PRIMARY KEY AUTOINCREMENT,
            longitude DECIMAL(9,6),
            latitude DECIMAL(9,6),
            date DATE,
            quantity INTEGER,
            notes TEXT,
            meanLongitude DECIMAL(9,6) NOT NULL,
            meanLatitude DECIMAL(9,6) NOT NULL,
            scientificName VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            professionalEmail VARCHAR(255),
            dateConfirmed DATE,
            FOREIGN KEY (meanLongitude, meanLatitude) REFERENCES location(meanLongitude, meanLatitude),
            FOREIGN KEY (scientificName) REFERENCES species(scientificName),
            FOREIGN KEY (email) REFERENCES user(email) ON DELETE CASCADE,
            FOREIGN KEY (professionalEmail) REFERENCES professional(email) ON DELETE SET NULL
            );

            CREATE TABLE media(
            observationID INTEGER,
            mediaID INTEGER,
            URL VARCHAR(500),
            mediaType VARCHAR(50),
            PRIMARY KEY (observationID, mediaID),
            FOREIGN KEY (observationID) REFERENCES observation(observationID) ON DELETE CASCADE
            );

            CREATE TABLE location(
            meanLongitude DECIMAL(9,6),
            meanLatitude DECIMAL(9,6),
            name VARCHAR(255),
            PRIMARY KEY (meanLongitude, meanLatitude)
            );

            CREATE TABLE groupChat_user(
            email VARCHAR(255),
            ID INTEGER,
            FOREIGN KEY (email) REFERENCES user(email) ON DELETE CASCADE,
            FOREIGN KEY (ID) references groupChat(ID) ON DELETE CASCADE,
            PRIMARY KEY (email, ID)
            );

            CREATE TABLE groupChat(
            ID INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255),
            created_at TIMESTAMP
            );

            CREATE TABLE message(
            ID INTEGER PRIMARY KEY AUTOINCREMENT,
            data TEXT,
            time_sent TIMESTAMP,
            group_chat_id INTEGER NOT NULL,
            email VARCHAR(255) NOT NULL,
            FOREIGN KEY (group_chat_id) REFERENCES groupChat(ID) ON DELETE CASCADE,
            FOREIGN KEY (email) REFERENCES user(email) ON DELETE CASCADE
            );

            CREATE TABLE phylum (
            phylum VARCHAR(255) PRIMARY KEY,
            kingdom VARCHAR(255) NOT NULL
            );

            CREATE TABLE class (
            class VARCHAR(255) PRIMARY KEY,
            phylum VARCHAR(255) NOT NULL,
            FOREIGN KEY (phylum) REFERENCES phylum(phylum)
            );

            CREATE TABLE family (
            family VARCHAR(255) PRIMARY KEY,
            class VARCHAR(255) NOT NULL,
            FOREIGN KEY (class) REFERENCES class(class)
            );

            CREATE TABLE `order` (
            `order` VARCHAR(255) PRIMARY KEY,
            family VARCHAR(255)NOT NULL,
            FOREIGN KEY (family) REFERENCES family(family)
            );

            CREATE TABLE genus (
            genus VARCHAR(255) PRIMARY KEY,
            `order` VARCHAR(255) NOT NULL,
            FOREIGN KEY (`order`) REFERENCES `order`(`order`)
            );

            CREATE TABLE species(
            scientificName VARCHAR(255) PRIMARY KEY,
            commonName VARCHAR(255),
            description TEXT,
            genus VARCHAR(255) NOT NULL,
            FOREIGN KEY (genus) REFERENCES genus(genus)
            );

            INSERT INTO genus (genus, `order`)
            VALUES ('Macrocystis', 'Laminariales'),
                   ('Ulva', 'Ulvales'),
                   ('Orcinus', 'Artiodactyla'),
                   ('Enteroctopus', 'Octopoda'),
                   ('Glaucus', 'Nudibranchia');

            INSERT INTO `order` (`order`, family)
            VALUES ('Laminariales', 'Laminariaceae'),
                   ('Ulvales', 'Ulvaceae'),
                   ('Artiodactyla', 'Delphinidae'),
                   ('Octopoda', 'Enteroctopodidae'),
                   ('Nudibranchia', 'Glaucidae');

            INSERT INTO family (family, class)
            VALUES ('Laminariaceae', 'Phaeophyceae'),
                   ('Ulvaceae', 'Ulvophyceae'),
                   ('Delphinidae', 'Mammalia'),
                   ('Enteroctopodidae', 'Cephalopoda'),
                   ('Glaucidae', 'Gastropoda');

            INSERT INTO class (class, phylum)
            VALUES ('Phaeophyceae', 'Ochrophyta'),
                   ('Ulvophyceae', 'Chlorophyta'),
                   ('Mammalia', 'Chordata'),
                   ('Cephalopoda', 'Mollusca'),
                   ('Gastropoda', 'Mollusca');

            INSERT INTO phylum (phylum, kingdom)
            VALUES ('Ochrophyta', 'Chromista'),
                   ('Chlorophyta', 'Plantae'),
                   ('Chordata', 'Animalia'),
                   ('Mollusca', 'Animalia'),
                   ('Rhodophyta', 'Plantae');

            INSERT INTO species (scientificName, commonName, description, genus)
            VALUES ('Macrocystis pyrifera', 'Giant Kelp', 'found this stuff everywhere, fills me with rage', 'Macrocystis'),
                   ('Ulva intestinalis', 'Sea lettuce', 'green, translucent, skinny tubes', 'Ulva'),
                   ('Ulva lactuca', 'Sea lettuce', 'green, translucent, sheets', 'Ulva'),
                   ('Orcinus orca', 'Orca', 'still mad I can’t hunt these', 'Orcinus'),
                   ('Enteroctopus dofleini', 'Giant Pacific Octopus', 'Red, 2m in diameter, depth of 30m, hiding in rocks', 'Enteroctopus');

            INSERT INTO location (meanLongitude, meanLatitude, name)
            VALUES ('49.245173', '-125.257978', 'Vancouver Island'),
                   ('49.267324', '-123.263471', 'Wreck Beach'),
                   ('49.330532', '-124.290541', 'EnglishMan River Estuary'),
                   ('49.405967', '-123.469368', 'Plumper Cove'),
                   ('49.451951', '-123.326787', 'Halkett Bay');

            INSERT INTO user (email, username, password)
            VALUES ('johnSmith@gmail.com', 'jsmithy', '1234'),
                   ('johnDoe@gmail.com', 'johnDoe', '12j3h1k2h3'),
                   ('gavinKrebbers@gmail.com', 'GBoy', 'ilovepasswords'),
                   ('janeDoe@gmail.com', 'jDoe', '1234'),
                   ('randomUser@gmail.com', 'jsmithy', '123N*!(2342d)'),
                   ('Simon@gmail.com', 'Simon', 'IlikeResearch'),
                   ('rachelResearch@gmail.com', 'rachelResearch', 'IlikeResearch'),
                   ('robertResearch@gmail.com', 'robertResearch', 'IlikeResearch'),
                   ('raoulResearch@gmail.com', 'raoulResearch', 'IlikeResearch'),
                   ('rdanielResearch@gmail.com', 'rdanielResearch', 'IlikeResearch'),
                   ('rowanResearch@gmail.com', 'rowanResearch', 'kjdssp');

            INSERT INTO professional (email, degree, certification, specialization)
            VALUES ('rachelResearch@gmail.com', 'Bachelors in Science', NULL, 'Fish'),
                   ('robertResearch@gmail.com', NULL, 'Data science from BCIT', 'Data Science'),
                   ('raoulResearch@gmail.com', 'Bachelors in Underwater Basket Weaving', NULL, 'Basket weaving'),
                   ('rdanielResearch@gmail.com', 'PHD in computer science', NULL, NULL),
                   ('Simon@gmail.com', 'Bachelors in Biology', NULL, 'Kelp'),
                   ('rowanResearch@gmail.com', NULL, 'Fish Xrays', NULL);

            INSERT INTO project (name, description)
            VALUES ('Clown Fish at Wreck Beach', 'This is a project tracking spottings of clown fish at Wreck beach near UBC'),
                   ('Coral Health Monitoring', 'Volenteers document coral reef health and biodiversity across vancouver island'),
                   ('Seastar documentation survey', 'A project tracking the population of  seastars and how many are effected by disease'),
                   ('Tidepool biodiversity', 'Participants photograph and document different species found in local tidepools'),
                   ('Kelp forest monitoring', 'Citizen monitor kelp forests along the coast of vancouver islands');

            INSERT INTO project_user (projectID, email)
            VALUES
            (1, 'johnDoe@gmail.com'),
            (1, 'johnSmith@gmail.com'),
            (3, 'janeDoe@gmail.com'),
            (3, 'gavinKrebbers@gmail.com'),
            (5, 'johnSmith@gmail.com'),
            (5, 'johnDoe@gmail.com'),
            (5, 'gavinKrebbers@gmail'),
            (5, 'janeDoe@gmail.com'),
            (5, 'randomUser@gmail.com');

            INSERT INTO project_professional (projectID, email)
            VALUES
            (1, 'Simon@gmail.com'),
            (1, 'rachelResearch@gmail.com'),
            (2, 'robertResearch@gmail.com'),
            (2, 'raoulResearch@gmail.com'),
            (3, 'rdanielResearch@gmail.com'),
            (3, 'rowanResearch@gmail.com'),
            (4, 'Simon@gmail.com'),
            (5, 'raoulResearch@gmail.com'),
            (5, 'rdanielResearch@gmail.com');


            INSERT INTO observation (longitude, latitude, date, quantity, notes, meanLongitude, meanLatitude, scientificName, email, professionalEmail, dateConfirmed)
            VALUES
            ('49.267100', '-123.263500', '2025-10-10', 3, 'Observed several kelp fronds near shore.', '49.267324', '-123.263471', 'Macrocystis pyrifera', 'johnSmith@gmail.com', 'Simon@gmail.com', '2025-10-11'),
            ('49.330600', '-124.290500', '2025-10-12', 1, 'Single kelp spotted in shallow waters.', '49.330532', '-124.290541', 'Macrocystis pyrifera', 'johnDoe@gmail.com', 'rachelResearch@gmail.com', '2025-10-13'),
            ('49.405900', '-123.469300', '2025-09-30', 5, 'Healthy kelp growth observed in cove.', '49.405967', '-123.469368', 'Macrocystis pyrifera', 'gavinKrebbers@gmail.com', NULL, NULL),
            ('49.451900', '-123.326700', '2025-10-01', 2, 'Sparse kelp patches noted.', '49.451951', '-123.326787', 'Macrocystis pyrifera', 'janeDoe@gmail.com', 'robertResearch@gmail.com', '2025-10-02'),
            ('49.245100', '-125.257900', '2025-09-28', 8, 'Large kelp forest area thriving.', '49.245173', '-125.257978', 'Macrocystis pyrifera', 'randomUser@gmail.com', 'Simon@gmail.com', '2025-09-29'),
            ('49.267200', '-123.263480', '2025-10-14', 4, 'Observed kelp growing around sea anemones.', '49.267324', '-123.263471', 'Macrocystis pyrifera', 'johnDoe@gmail.com', 'rachelResearch@gmail.com', '2025-10-15'),
            ('49.330550', '-124.290510', '2025-10-16', 2, 'Small kelp patches near coral structures.', '49.330532', '-124.290541', 'Macrocystis pyrifera', 'johnSmith@gmail.com', NULL, NULL),
            ('49.245120', '-125.257950', '2025-10-17', 6, 'Multiple young kelp fronds sighted in kelp forest region.', '49.245173', '-125.257978', 'Macrocystis pyrifera', 'gavinKrebbers@gmail.com', 'raoulResearch@gmail.com', '2025-10-18'),
            ('49.405950', '-123.469350', '2025-10-02', 5, 'Several kelp plants found on rocks during low tide.', '49.405967', '-123.469368', 'Macrocystis pyrifera', 'janeDoe@gmail.com', 'rowanResearch@gmail.com', '2025-10-03'),
            ('49.451930', '-123.326760', '2025-10-04', 3, 'Kelp observed with signs of stress or damage.', '49.451951', '-123.326787', 'Macrocystis pyrifera', 'johnSmith@gmail.com', 'robertResearch@gmail.com', '2025-10-05'),
            ('49.330520', '-124.290520', '2025-10-06', 7, 'Healthy kelp population noted around reef area.', '49.330532', '-124.290541', 'Macrocystis pyrifera', 'gavinKrebbers@gmail.com', 'Simon@gmail.com', '2025-10-07');

            INSERT INTO project_observation (projectID, observationID)
            VALUES (5, 1),(5, 2),(5, 3),(5, 4),(5, 5),(1, 6),(1, 7),(1, 8),(3, 9),(3, 10),(3, 11);

            INSERT INTO media (observationID, mediaID, URL, mediaType)
            VALUES
            (1, 1, 'https://example.com/media/kelp_shore_1.jpg', 'image'),
            (1, 2, 'https://example.com/media/kelp_shore_2.mp4', 'video'),
            (2, 1, 'https://example.com/media/kelp_shallow_1.jpg', 'image'),
            (3, 1, 'https://example.com/media/kelp_cove_1.jpg', 'image'),
            (3, 2, 'https://example.com/media/kelp_cove_2.jpg', 'image'),
            (4, 1, 'https://example.com/media/kelp_sparse_1.jpg', 'image'),
            (5, 1, 'https://example.com/media/kelp_forest_1.jpg', 'image'),
            (6, 1, 'https://example.com/media/kelp_anemones_1.jpg', 'image'),
            (7, 1, 'https://example.com/media/kelp_coral_1.jpg', 'image'),
            (8, 1, 'https://example.com/media/kelp_young_1.jpg', 'image'),
            (9, 1, 'https://example.com/media/kelp_rocks_1.jpg', 'image'),
            (10, 1, 'https://example.com/media/kelp_stress_1.jpg', 'image'),
            (11, 1, 'https://example.com/media/kelp_reef_1.jpg', 'image');

            INSERT INTO groupChat (id, `name`, created_at)
            VALUES (0, 'the fish boys', '2025-10-18 14:30:00'),
                   (1, 'the pufferfish boys', '2022-10-18 6:30:00'),
                   (2, 'the coral boys', '1-10-18 14:30:00'),
                   (3, 'empty groupchat', '2025-11-18 14:30:00'),
                   (4, 'dead groupchat because two people in the chat got into a heated argument', '2025-10-18 14:30:00');

            INSERT INTO groupChat_user (email, id)
            VALUES ('Simon@gmail.com', 0),
                   ('Simon@gmail.com', 1),
                   ('rachelResearch@gmail.com', 1),
                   ('randomUser@gmail.com', 0),
                   ('robertResearch@gmail.com', 2);

            INSERT INTO message (id, data, time_sent, group_chat_id, email)
            VALUES
            (0, 'hi nice to meet you', '2025-10-18 14:30:00', 0, 'Simon@gmail.com'),
            (1, 'hi not nice to meet you', '2025-10-18 14:30:01', 0, 'rachelResearch@gmail.com'),
            (2, 'aslkdjfalsdkfjlaskjfdlsajfalksjdfasdflkjsakdjflawoeifjwoefjoewfjoewijfoewijf', '2025-10-19 16:36:02', 0, 'johnSmith@gmail.com'),
            (3, 'asdf', '2022-10-18 14:30:00', 0, 'johnDoe@gmail.com'),
            (4, 'I love clownfish, this is the clownfish group', '2025-10-18 14:30:00', 1, 'rachelResearch@gmail.com'),
            (5, 'hello coral porject peopel ', '2025-10-18 15:00:00', 2, 'robertResearch@gmail.com'),
            (6, 'wow the tide sure did change today ', '2025-10-19 09:45:00', 3, 'janeDoe@gmail.com'),
            (7, 'i swa so much kelp you wont beleive it ', '2025-10-19 10:15:00', 4, 'gavinKrebbers@gmail.com'),
            (8, 'i like petting sturgeons', '2025-10-19 11:00:00', 0, 'Simon@gmail.com'),
            (9, 'i found 3 star fish', '2025-10-19 11:30:00', 3, 'johnSmith@gmail.com');
        ");

        DB::statement('PRAGMA foreign_keys = ON;');
    }
}
