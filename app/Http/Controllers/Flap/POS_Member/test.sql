use example_;
BEGIN
DECLARE @TableNewCustomer TABLE (
	serNo varchar(40)
);
DECLARE @serNo varchar(40);

EXEC @serNo = dbo.chinghwa_fnGetNewMemberTCode

INSERT INTO @TableNewCustomer (serNo) VALUES(@serNo);
END
SELECT * FROM @TableNewCustomer;
