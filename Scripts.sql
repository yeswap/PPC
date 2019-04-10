CREATE TABLE tmptable_1 SELECT * FROM Plans WHERE OperatorID = 92;
ALTER TABLE tmptable_1 MODIFY ID int(11) null;
UPDATE tmptable_1 SET ID = NULL, OperatorID = 93, AllowedDevices= 'Sprint or universal unlocked';
INSERT INTO Plans SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

CREATE TABLE tmptable_1 SELECT * FROM DataAddons WHERE PlanID = 529;
ALTER TABLE tmptable_1 MODIFY DataAddonID int(11) null;
UPDATE tmptable_1 SET DataAddonID = NULL, PlanID = 599;
INSERT INTO DataAddons SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

CREATE TABLE tmptable_1 SELECT * FROM FamilyPlans WHERE PlanID = 645;
ALTER TABLE tmptable_1 MODIFY FamilyPlanID int(11) null;
UPDATE tmptable_1 SET FamilyPlanID = NULL, PlanID = 652;
INSERT INTO FamilyPlans SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

CREATE TABLE tmptable_1 SELECT * FROM VoiceAddons WHERE PlanID = 529;
ALTER TABLE tmptable_1 MODIFY VoiceAddonID int(11) null;
UPDATE tmptable_1 SET VoiceAddonID = NULL, PlanID = 446;
INSERT INTO VoiceAddons SELECT * FROM tmptable_1;
DROP TABLE IF EXISTS tmptable_1;

CREATE TABLE tmptable_1 SELECT * FROM TextAddons WHERE PlanID = 529;
ALTER TABLE tmptable_1 MODIFY TextAddonID int(11) null;
UPDATE tmptable_1 SET TextAddonID = NULL, PlanID = 603;
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