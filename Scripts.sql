CREATE TABLE tmptable_1 SELECT * FROM Plans WHERE OperatorID = 100;
ALTER TABLE tmptable_1 MODIFY ID int(11) null;
UPDATE tmptable_1 SET ID = NULL, OperatorID = 123;
INSERT INTO Plans SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

CREATE TABLE tmptable_1 SELECT * FROM DataAddons WHERE PlanID = 686;
ALTER TABLE tmptable_1 MODIFY DataAddonID int(11) null;
UPDATE tmptable_1 SET DataAddonID = NULL, PlanID = 1060;
INSERT INTO DataAddons SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

CREATE TABLE tmptable_1 SELECT * FROM FamilyPlans WHERE PlanID = 496;
ALTER TABLE tmptable_1 MODIFY FamilyPlanID int(11) null;
UPDATE tmptable_1 SET FamilyPlanID = NULL, PlanID = 864;
INSERT INTO FamilyPlans SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

CREATE TABLE tmptable_1 SELECT * FROM VoiceAddons WHERE PlanID = 720;
ALTER TABLE tmptable_1 MODIFY VoiceAddonID int(11) null;
UPDATE tmptable_1 SET VoiceAddonID = NULL, PlanID = 722;
INSERT INTO VoiceAddons SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

CREATE TABLE tmptable_1 SELECT * FROM TextAddons WHERE PlanID = 720;
ALTER TABLE tmptable_1 MODIFY TextAddonID int(11) null;
UPDATE tmptable_1 SET TextAddonID = NULL, PlanID = 722;
INSERT INTO TextAddons SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

CREATE TABLE tmptable_1 SELECT * FROM Plans WHERE OperatorID = 74 and Name = "Triple Triple";
ALTER TABLE tmptable_1 MODIFY ID int(11) null;
UPDATE tmptable_1 SET ID = NULL, OperatorID = 76, AllowedDevices= 'T-Mobile or GSM unlocked';
INSERT INTO Plans SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

select Plans.MonthlyCost as MonthlyCost, Operators.Name as Operator, Networks.Name as Network, Plans.Name as Plan, CASE
WHEN Minutes = 0 THEN "None"
WHEN Minutes = -1 THEN "Unlimited"
WHEN Minutes > 1 THEN Minutes
END as Minutes, Texts, Data, OverageThrottle, Plans.Notes as Notes, isPayGo  from Plans join Operators on Plans.OperatorID = Operators.OperatorID join Networks on Operators.NetworkID = Networks.NetworkID

select Plans.MonthlyCost as MonthlyCost, Operators.Name as Operator, Networks.Name as Network, Plans.Name as Plan, Minutes, Texts, Data, OverageThrottle, Plans.Notes as Notes, isPayGo  from Plans join Operators on Plans.OperatorID = Operators.OperatorID join Networks on Operators.NetworkID = Networks.NetworkID

CREATE TABLE tmptable_1 SELECT * FROM Plans WHERE ID = 720;
ALTER TABLE tmptable_1 MODIFY ID int(11) null;
UPDATE tmptable_1 SET ID = NULL, OperatorID = 76, AllowedDevices= 'T-Mobile or unlocked GSM';
INSERT INTO Plans SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

DELETE FROM `VoiceAddons` WHERE `PlanID` = 835

SELECT o.name, o.NameSuffix, p.Name, 5G FROM `Plans` p join Operators o on p.OperatorID = o.OperatorID where 5G <> '' ORDER by o.Name, p.Name

Update `Plans` set 5G = 1 WHERE OperatorID = 65