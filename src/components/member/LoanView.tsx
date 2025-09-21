import React, { useState } from 'react';
import { User, Loan, AppState } from '../../App';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '../ui/select';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '../ui/table';
import { Badge } from '../ui/badge';
import { Progress } from '../ui/progress';
import { Plus, CreditCard, FileText, Eye, Printer, Calendar, DollarSign } from 'lucide-react';
import { toast } from 'sonner@2.0.3';
import { LoanAgreement } from './LoanAgreement';

interface LoanViewProps {
  member: User;
  loans: Loan[];
  state: AppState;
  updateState: (updates: Partial<AppState>) => void;
}

export function LoanView({ member, loans, state, updateState }: LoanViewProps) {
  const [applyLoanOpen, setApplyLoanOpen] = useState(false);
  const [agreementOpen, setAgreementOpen] = useState(false);
  const [selectedLoanForAgreement, setSelectedLoanForAgreement] = useState<Loan | null>(null);
  const [newLoan, setNewLoan] = useState({
    amount: '',
    termMonths: '6'
  });

  const activeLoans = loans.filter(loan => loan.status === 'active');
  const pendingLoans = loans.filter(loan => loan.status === 'pending');
  const totalBorrowed = activeLoans.reduce((sum, loan) => sum + loan.amount, 0);

  const handleApplyLoan = () => {
    if (!newLoan.amount || parseFloat(newLoan.amount) <= 0) {
      toast.error('Please enter a valid amount');
      return;
    }

    const termMonths = parseInt(newLoan.termMonths);
    const amount = parseFloat(newLoan.amount);
    const interestRate = state.interestRate;
    
    // Calculate monthly payment for bi-monthly payments (twice per month)
    const totalPayments = termMonths * 2; // 2 payments per month
    const interestPerPayment = interestRate / 2 / 100; // Half of annual rate per payment
    const monthlyPayment = Math.round((amount / totalPayments) + (amount * interestPerPayment));

    const loan: Loan = {
      id: `loan_${Date.now()}`,
      memberId: member.memberId!,
      amount,
      termMonths,
      interestRate,
      status: 'pending',
      applicationDate: new Date().toISOString().split('T')[0],
      monthlyPayment,
      paymentsMade: 0,
      totalPayments,
      agreementSigned: false
    };

    // Add notification for admin
    const notification = {
      id: `notif_${Date.now()}`,
      type: 'loan_application' as const,
      memberId: member.memberId!,
      memberName: member.name,
      message: `New loan application for ${formatCurrency(amount)}`,
      date: new Date().toISOString().split('T')[0],
      status: 'pending' as const,
      data: {
        amount,
        termMonths
      }
    };

    updateState({
      loans: [...state.loans, loan],
      notifications: [...state.notifications, notification]
    });

    toast.success('Loan application submitted successfully');
    setApplyLoanOpen(false);
    setNewLoan({ amount: '', termMonths: '6' });
  };

  const handleViewAgreement = (loan?: Loan) => {
    if (loan) {
      setSelectedLoanForAgreement(loan);
    }
    setAgreementOpen(true);
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(amount);
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  const getStatusBadge = (status: string) => {
    const variants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
      'pending': 'outline',
      'approved': 'secondary',
      'active': 'default',
      'completed': 'secondary',
      'rejected': 'destructive'
    };
    return <Badge variant={variants[status] || 'outline'}>{status.charAt(0).toUpperCase() + status.slice(1)}</Badge>;
  };

  const calculateProgress = (loan: Loan) => {
    return loan.totalPayments > 0 ? (loan.paymentsMade / loan.totalPayments) * 100 : 0;
  };

  return (
    <div className="max-w-6xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-semibold">Loans</h2>
          <p className="text-muted-foreground">Manage your loan applications and payments</p>
        </div>
        <div className="flex space-x-2">
          <Button variant="outline" onClick={() => handleViewAgreement()}>
            <FileText className="w-4 h-4 mr-2" />
            View Agreement
          </Button>
          <Dialog open={applyLoanOpen} onOpenChange={setApplyLoanOpen}>
            <DialogTrigger asChild>
              <Button>
                <Plus className="w-4 h-4 mr-2" />
                Apply for Loan
              </Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Apply for New Loan</DialogTitle>
                <DialogDescription>
                  Fill out the loan application form. Current interest rate: {state.interestRate}% annually
                </DialogDescription>
              </DialogHeader>
              <div className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="loan-amount">Loan Amount</Label>
                  <Input
                    id="loan-amount"
                    type="number"
                    placeholder="Enter loan amount"
                    value={newLoan.amount}
                    onChange={(e) => setNewLoan(prev => ({ ...prev, amount: e.target.value }))}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="loan-term">Loan Term</Label>
                  <Select
                    value={newLoan.termMonths}
                    onValueChange={(value) => setNewLoan(prev => ({ ...prev, termMonths: value }))}
                  >
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="3">3 Months</SelectItem>
                      <SelectItem value="6">6 Months</SelectItem>
                      <SelectItem value="12">12 Months</SelectItem>
                      <SelectItem value="18">18 Months</SelectItem>
                      <SelectItem value="24">24 Months</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="p-4 bg-blue-50 rounded-lg">
                  <h4 className="font-medium mb-2">Payment Information</h4>
                  <p className="text-sm text-gray-600 mb-2">
                    • Payments are made twice monthly (15th and 30th)
                  </p>
                  <p className="text-sm text-gray-600 mb-2">
                    • Interest rate: {state.interestRate}% annually (1% per payment)
                  </p>
                  <p className="text-sm text-gray-600">
                    • Total payments: {parseInt(newLoan.termMonths) * 2}
                  </p>
                </div>
                <div className="flex justify-end space-x-2">
                  <Button variant="outline" onClick={() => setApplyLoanOpen(false)}>
                    Cancel
                  </Button>
                  <Button onClick={handleApplyLoan}>
                    Submit Application
                  </Button>
                </div>
              </div>
            </DialogContent>
          </Dialog>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid md:grid-cols-3 gap-6">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Active Loans</CardTitle>
            <CreditCard className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{activeLoans.length}</div>
            <p className="text-xs text-muted-foreground">
              Total borrowed: {formatCurrency(totalBorrowed)}
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Pending Applications</CardTitle>
            <Calendar className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{pendingLoans.length}</div>
            <p className="text-xs text-muted-foreground">
              Awaiting approval
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Interest Rate</CardTitle>
            <DollarSign className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{state.interestRate}%</div>
            <p className="text-xs text-muted-foreground">
              Annual rate (1% per payment)
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Loans Table */}
      <Card>
        <CardHeader>
          <CardTitle>Loan History</CardTitle>
          <CardDescription>
            All your loan applications and current loans
          </CardDescription>
        </CardHeader>
        <CardContent>
          {loans.length === 0 ? (
            <div className="text-center py-8">
              <CreditCard className="h-12 w-12 text-muted-foreground mx-auto mb-4" />
              <h3 className="text-lg font-medium mb-2">No loans yet</h3>
              <p className="text-muted-foreground mb-4">
                Apply for your first loan to get started
              </p>
              <Button onClick={() => setApplyLoanOpen(true)}>
                <Plus className="w-4 h-4 mr-2" />
                Apply for Loan
              </Button>
            </div>
          ) : (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Application Date</TableHead>
                  <TableHead>Amount</TableHead>
                  <TableHead>Term</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Progress</TableHead>
                  <TableHead>Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {loans
                  .sort((a, b) => new Date(b.applicationDate).getTime() - new Date(a.applicationDate).getTime())
                  .map((loan) => (
                    <TableRow key={loan.id}>
                      <TableCell>{formatDate(loan.applicationDate)}</TableCell>
                      <TableCell className="font-medium">{formatCurrency(loan.amount)}</TableCell>
                      <TableCell>{loan.termMonths} months</TableCell>
                      <TableCell>{getStatusBadge(loan.status)}</TableCell>
                      <TableCell>
                        {loan.status === 'active' ? (
                          <div className="space-y-1">
                            <div className="flex justify-between text-sm">
                              <span>{loan.paymentsMade}/{loan.totalPayments}</span>
                              <span>{Math.round(calculateProgress(loan))}%</span>
                            </div>
                            <Progress value={calculateProgress(loan)} className="h-2" />
                          </div>
                        ) : (
                          <span className="text-muted-foreground">-</span>
                        )}
                      </TableCell>
                      <TableCell>
                        <div className="flex space-x-2">
                          {(loan.status === 'active' || loan.status === 'approved') && (
                            <Button
                              variant="outline"
                              size="sm"
                              onClick={() => handleViewAgreement(loan)}
                            >
                              <Eye className="w-4 h-4" />
                            </Button>
                          )}
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
              </TableBody>
            </Table>
          )}
        </CardContent>
      </Card>

      {/* Loan Agreement Dialog */}
      <Dialog open={agreementOpen} onOpenChange={setAgreementOpen}>
        <DialogContent className="max-w-4xl max-h-[80vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Loan Agreement</DialogTitle>
            <DialogDescription>
              {selectedLoanForAgreement 
                ? `Loan Agreement for ${formatCurrency(selectedLoanForAgreement.amount)}`
                : 'Standard Loan Agreement Template'
              }
            </DialogDescription>
          </DialogHeader>
          <LoanAgreement 
            loan={selectedLoanForAgreement}
            member={member}
            interestRate={state.interestRate}
          />
        </DialogContent>
      </Dialog>
    </div>
  );
}