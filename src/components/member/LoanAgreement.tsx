import React from 'react';
import { Loan, User } from '../../App';
import { Button } from '../ui/button';
import { Separator } from '../ui/separator';
import { Printer } from 'lucide-react';

interface LoanAgreementProps {
  loan?: Loan;
  member: User;
  interestRate: number;
}

export function LoanAgreement({ loan, member, interestRate }: LoanAgreementProps) {
  const handlePrint = () => {
    window.print();
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(amount);
  };

  const formatDate = (dateString?: string) => {
    if (!dateString) return new Date().toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
    
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-end print:hidden">
        <Button onClick={handlePrint} variant="outline">
          <Printer className="w-4 h-4 mr-2" />
          Print Agreement
        </Button>
      </div>

      <div className="space-y-6 print:text-black print:bg-white">
        {/* Header */}
        <div className="text-center space-y-2">
          <h1 className="text-2xl font-bold">LOAN AGREEMENT</h1>
          <p className="text-muted-foreground">LoanSystem Financial Services</p>
        </div>

        <Separator />

        {/* Agreement Details */}
        <div className="space-y-4">
          <h2 className="text-lg font-semibold">LOAN AGREEMENT DETAILS</h2>
          
          <div className="grid md:grid-cols-2 gap-4">
            <div>
              <p><strong>Borrower Name:</strong> {member.name}</p>
              <p><strong>Member ID:</strong> {member.memberId}</p>
              <p><strong>Email:</strong> {member.email}</p>
              <p><strong>Mobile:</strong> {member.mobile}</p>
            </div>
            <div>
              <p><strong>Loan Amount:</strong> {loan ? formatCurrency(loan.amount) : '[AMOUNT]'}</p>
              <p><strong>Term:</strong> {loan ? loan.termMonths : '[TERM]'} months</p>
              <p><strong>Interest Rate:</strong> {interestRate}% per annum</p>
              <p><strong>Agreement Date:</strong> {formatDate(loan?.approvalDate)}</p>
            </div>
          </div>
        </div>

        <Separator />

        {/* Payment Schedule */}
        <div className="space-y-4">
          <h2 className="text-lg font-semibold">PAYMENT SCHEDULE</h2>
          
          <div className="bg-gray-50 p-4 rounded-lg print:border print:border-gray-300">
            <div className="grid md:grid-cols-2 gap-4">
              <div>
                <p><strong>Payment Frequency:</strong> Twice Monthly</p>
                <p><strong>Payment Dates:</strong> 15th and 30th of each month</p>
                <p><strong>Total Payments:</strong> {loan ? loan.totalPayments : '[TOTAL_PAYMENTS]'}</p>
              </div>
              <div>
                <p><strong>Payment Amount:</strong> {loan ? formatCurrency(loan.monthlyPayment) : '[PAYMENT_AMOUNT]'}</p>
                <p><strong>Interest per Payment:</strong> 1% of principal</p>
                <p><strong>Start Date:</strong> {formatDate(loan?.startDate)}</p>
              </div>
            </div>
          </div>
        </div>

        <Separator />

        {/* Terms and Conditions */}
        <div className="space-y-4">
          <h2 className="text-lg font-semibold">TERMS AND CONDITIONS</h2>
          
          <div className="space-y-3 text-sm">
            <div>
              <h3 className="font-medium">1. Interest Calculation</h3>
              <p>The annual interest rate of {interestRate}% is divided equally between two monthly payments. Each payment will include 1% interest on the principal amount plus a portion of the principal.</p>
            </div>

            <div>
              <h3 className="font-medium">2. Payment Schedule</h3>
              <p>Payments are due twice monthly on the 15th and 30th (or last day) of each month. Late payments may incur additional fees and affect the borrower's standing.</p>
            </div>

            <div>
              <h3 className="font-medium">3. Early Repayment</h3>
              <p>The borrower may repay the loan in full at any time without penalty. Interest will be calculated only for the period the loan was outstanding.</p>
            </div>

            <div>
              <h3 className="font-medium">4. Default</h3>
              <p>Failure to make payments as scheduled may result in default status. The lender reserves the right to take appropriate action to recover the outstanding amount.</p>
            </div>

            <div>
              <h3 className="font-medium">5. Member Responsibilities</h3>
              <p>The borrower agrees to maintain active membership status and keep contact information current. Any changes to personal information must be reported immediately.</p>
            </div>

            <div>
              <h3 className="font-medium">6. Modification</h3>
              <p>This agreement may only be modified with written consent from both parties. All modifications must be approved by the system administrator.</p>
            </div>

            <div>
              <h3 className="font-medium">7. Governing Law</h3>
              <p>This agreement shall be governed by the laws of the jurisdiction where the lending organization is established.</p>
            </div>
          </div>
        </div>

        <Separator />

        {/* Acknowledgment */}
        <div className="space-y-4">
          <h2 className="text-lg font-semibold">ACKNOWLEDGMENT</h2>
          
          <p className="text-sm">
            By submitting this loan application and accepting the loan terms, the borrower acknowledges that they have read, understood, and agree to be bound by all terms and conditions set forth in this agreement.
          </p>

          <div className="grid md:grid-cols-2 gap-8 mt-8">
            <div className="space-y-2">
              <p className="font-medium">Borrower Signature:</p>
              <div className="border-b border-gray-300 h-8"></div>
              <p className="text-sm">{member.name}</p>
              <p className="text-sm">Date: {formatDate()}</p>
            </div>

            <div className="space-y-2">
              <p className="font-medium">Lender Representative:</p>
              <div className="border-b border-gray-300 h-8"></div>
              <p className="text-sm">System Administrator</p>
              <p className="text-sm">Date: {formatDate()}</p>
            </div>
          </div>
        </div>

        <Separator />

        {/* Footer */}
        <div className="text-center text-xs text-muted-foreground">
          <p>LoanSystem Financial Services</p>
          <p>This document was generated on {formatDate()}</p>
          {loan && <p>Loan Reference: {loan.id}</p>}
        </div>
      </div>
    </div>
  );
}