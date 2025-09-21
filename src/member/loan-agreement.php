<?php
// This file is included in member/loans.php
if (!isset($user)) {
    exit('Access denied');
}

$interestRate = getInterestRate();
?>

<div class="grid gap-6 print:text-black print:bg-white">
    <!-- Header -->
    <div class="text-center grid gap-2">
        <h1 class="text-2xl font-bold">LOAN AGREEMENT</h1>
        <p class="text-muted-foreground">LoanSystem Financial Services</p>
    </div>

    <hr>

    <!-- Agreement Details -->
    <div class="grid gap-4">
        <h2 class="text-lg font-semibold">LOAN AGREEMENT DETAILS</h2>
        
        <div class="grid grid-cols-1 gap-4" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            <div>
                <p><strong>Borrower Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Member ID:</strong> <?php echo htmlspecialchars($user['member_id']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Mobile:</strong> <?php echo htmlspecialchars($user['mobile']); ?></p>
            </div>
            <div>
                <p><strong>Loan Amount:</strong> [AMOUNT]</p>
                <p><strong>Term:</strong> [TERM] months</p>
                <p><strong>Interest Rate:</strong> <?php echo $interestRate; ?>% per annum</p>
                <p><strong>Agreement Date:</strong> <?php echo formatDate(date('Y-m-d')); ?></p>
            </div>
        </div>
    </div>

    <hr>

    <!-- Payment Schedule -->
    <div class="grid gap-4">
        <h2 class="text-lg font-semibold">PAYMENT SCHEDULE</h2>
        
        <div class="p-4 bg-muted rounded">
            <div class="grid grid-cols-1 gap-4" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                <div>
                    <p><strong>Payment Frequency:</strong> Twice Monthly</p>
                    <p><strong>Payment Dates:</strong> 15th and 30th of each month</p>
                    <p><strong>Total Payments:</strong> [TOTAL_PAYMENTS]</p>
                </div>
                <div>
                    <p><strong>Payment Amount:</strong> [PAYMENT_AMOUNT]</p>
                    <p><strong>Interest per Payment:</strong> <?php echo $interestRate/2; ?>% of principal</p>
                    <p><strong>Start Date:</strong> [START_DATE]</p>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Terms and Conditions -->
    <div class="grid gap-4">
        <h2 class="text-lg font-semibold">TERMS AND CONDITIONS</h2>
        
        <div class="grid gap-3 text-sm">
            <div>
                <h3 class="font-medium">1. Interest Calculation</h3>
                <p>The annual interest rate of <?php echo $interestRate; ?>% is divided equally between two monthly payments. Each payment will include <?php echo $interestRate/2; ?>% interest on the principal amount plus a portion of the principal.</p>
            </div>

            <div>
                <h3 class="font-medium">2. Payment Schedule</h3>
                <p>Payments are due twice monthly on the 15th and 30th (or last day) of each month. Late payments may incur additional fees and affect the borrower's standing.</p>
            </div>

            <div>
                <h3 class="font-medium">3. Early Repayment</h3>
                <p>The borrower may repay the loan in full at any time without penalty. Interest will be calculated only for the period the loan was outstanding.</p>
            </div>

            <div>
                <h3 class="font-medium">4. Default</h3>
                <p>Failure to make payments as scheduled may result in default status. The lender reserves the right to take appropriate action to recover the outstanding amount.</p>
            </div>

            <div>
                <h3 class="font-medium">5. Member Responsibilities</h3>
                <p>The borrower agrees to maintain active membership status and keep contact information current. Any changes to personal information must be reported immediately.</p>
            </div>

            <div>
                <h3 class="font-medium">6. Modification</h3>
                <p>This agreement may only be modified with written consent from both parties. All modifications must be approved by the system administrator.</p>
            </div>

            <div>
                <h3 class="font-medium">7. Governing Law</h3>
                <p>This agreement shall be governed by the laws of the jurisdiction where the lending organization is established.</p>
            </div>
        </div>
    </div>

    <hr>

    <!-- Acknowledgment -->
    <div class="grid gap-4">
        <h2 class="text-lg font-semibold">ACKNOWLEDGMENT</h2>
        
        <p class="text-sm">
            By submitting this loan application and accepting the loan terms, the borrower acknowledges that they have read, understood, and agree to be bound by all terms and conditions set forth in this agreement.
        </p>

        <div class="grid grid-cols-1 gap-8 mt-8" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            <div class="grid gap-2">
                <p class="font-medium">Borrower Signature:</p>
                <div class="border-b border-gray-300 h-8"></div>
                <p class="text-sm"><?php echo htmlspecialchars($user['name']); ?></p>
                <p class="text-sm">Date: <?php echo formatDate(date('Y-m-d')); ?></p>
            </div>

            <div class="grid gap-2">
                <p class="font-medium">Lender Representative:</p>
                <div class="border-b border-gray-300 h-8"></div>
                <p class="text-sm">System Administrator</p>
                <p class="text-sm">Date: <?php echo formatDate(date('Y-m-d')); ?></p>
            </div>
        </div>
    </div>

    <hr>

    <!-- Footer -->
    <div class="text-center text-sm text-muted-foreground">
        <p>LoanSystem Financial Services</p>
        <p>This document was generated on <?php echo formatDate(date('Y-m-d')); ?></p>
    </div>
</div>