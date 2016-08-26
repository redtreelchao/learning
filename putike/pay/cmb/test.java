import java.lang.*;
import cmb.netpayment.Settle;
import java.io.*;
class test {
    static private Settle settle;
    static private int iRet;
    private static void testQueryUnsettledOrder()
    {
    	StringBuffer strbuf = new StringBuffer();
    	iRet = settle.QueryUnsettledOrder(strbuf);
    	if (iRet == 0)
		{
			System.out.println("QueryUnsettledOrder ok");
			System.out.println(strbuf.toString());
    	}
    	else
		{
			System.out.println(settle.GetLastErr(iRet));
		}
    }
    private static void testQuerySettledOrder()
    {
    	StringBuffer strbuf = new StringBuffer();
    	iRet = settle.QuerySettledOrder("20040101","20041010",strbuf);
    	if (iRet == 0)
		{
			System.out.println("QuerySettledOrder ok");
			System.out.println(strbuf.toString());
    	}
    	else
		{
			System.out.println(settle.GetLastErr(iRet));
			System.out.println(settle.GetLastErr(iRet));
		}
    }
    private static void testQueryTransact()
    {
    	StringBuffer strbuf = new StringBuffer();
    	iRet = settle.QueryTransact("20040302",strbuf);
    	if (iRet == 0)
		{
			System.out.println("QueryTransact ok");
			System.out.println(strbuf.toString());
    	}
    	else
		{
			System.out.println(settle.GetLastErr(iRet));
			System.out.println(settle.GetLastErr(iRet));
		}
    }
    private static void testQueryPagedSettledOrder()
    {
    	StringBuffer strbuf = new StringBuffer();
        settle.PageReset();
        do
        {
    	    iRet = settle.QuerySettledOrderByPage("20040101","20041010",10,strbuf);
        } while (iRet==0 && !settle.m_bIsLastPage);
    	if (iRet == 0)
		{
			System.out.println("QuerySettledOrder ok");
			System.out.println(strbuf.toString());
    	}
    	else
		{
			System.out.println(settle.GetLastErr(iRet));
			System.out.println(settle.GetLastErr(iRet));
		}
    }
    private static void testQueryPagedTransact()
    {
    	StringBuffer strbuf = new StringBuffer();
        settle.PageReset();
        do
        {
    	    iRet = settle.QueryTransactByPage("20040302",10,strbuf);
        } while (iRet==0 && !settle.m_bIsLastPage);
    	if (iRet == 0)
		{
			System.out.println("QueryTransact ok");
			System.out.println(strbuf.toString());
    	}
    	else
		{
			System.out.println(settle.GetLastErr(iRet));
			System.out.println(settle.GetLastErr(iRet));
		}
    }
    private static void testVerifySign(String signature)
    {
		try
		{
			cmb.netpayment.Security pay = new cmb.netpayment.Security("public.key");

            byte[] baSig = signature.getBytes();
			boolean bRet = pay.checkInfoFromBank(baSig);
			System.out.println("checkInfoFromBank: "+bRet);
		}
		catch(Exception e)
		{
			System.out.println("new netpayment object failed: "+e.getMessage());
		}
    }

    public static void main(String args[])
    {
        settle=new Settle();
    	iRet = settle.SetOptions("payment.ebank.cmbchina.com");
   		if (iRet == 0)
		{
			System.out.println("SetOptions ok");
    	}
    	else
		{
			System.out.println(settle.GetLastErr(iRet));
			System.out.println(settle.GetLastErr(iRet));
			return;
		}

    	iRet = settle.LoginC("0755","000107","888888");
    	if (iRet == 0)
		{
			System.out.println("LoginC ok");
	    }
    	else
		{
			System.out.println(settle.GetLastErr(iRet));
			return;
		}
        testQueryUnsettledOrder();

        settle.Logout();

        String signature = args[0];
        testVerifySign(signature);
    }
}
