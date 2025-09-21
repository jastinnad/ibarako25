import React, { useState } from 'react';
import { User, Contribution, AppState } from '../../App';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '../ui/card';
import { Button } from '../ui/button';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '../ui/select';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '../ui/dialog';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '../ui/table';
import { Badge } from '../ui/badge';
import { Plus, DollarSign, TrendingUp, Calendar } from 'lucide-react';
import { toast } from 'sonner@2.0.3';

interface ContributionViewProps {
  member: User;
  contributions: Contribution[];
  state: AppState;
  updateState: (updates: Partial<AppState>) => void;
}

export function ContributionView({ member, contributions, state, updateState }: ContributionViewProps) {
  const [addContributionOpen, setAddContributionOpen] = useState(false);
  const [newContribution, setNewContribution] = useState({
    amount: '',
    type: 'monthly' as 'monthly' | 'additional',
    description: ''
  });

  const totalContributions = contributions.reduce((sum, cont) => sum + cont.amount, 0);
  const monthlyContributions = contributions.filter(cont => cont.type === 'monthly');
  const additionalContributions = contributions.filter(cont => cont.type === 'additional');

  const handleAddContribution = () => {
    if (!newContribution.amount || parseFloat(newContribution.amount) <= 0) {
      toast.error('Please enter a valid amount');
      return;
    }

    const contribution: Contribution = {
      id: `cont_${Date.now()}`,
      memberId: member.memberId!,
      amount: parseFloat(newContribution.amount),
      date: new Date().toISOString().split('T')[0],
      type: newContribution.type,
      description: newContribution.description || `${newContribution.type} contribution`
    };

    updateState({
      contributions: [...state.contributions, contribution]
    });

    toast.success('Contribution added successfully');
    setAddContributionOpen(false);
    setNewContribution({ amount: '', type: 'monthly', description: '' });
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

  return (
    <div className="max-w-6xl mx-auto space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-semibold">Contributions</h2>
          <p className="text-muted-foreground">Manage your savings and contributions</p>
        </div>
        <Dialog open={addContributionOpen} onOpenChange={setAddContributionOpen}>
          <DialogTrigger asChild>
            <Button>
              <Plus className="w-4 h-4 mr-2" />
              Add Contribution
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Add New Contribution</DialogTitle>
              <DialogDescription>
                Add a new contribution to your account
              </DialogDescription>
            </DialogHeader>
            <div className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="amount">Amount</Label>
                <Input
                  id="amount"
                  type="number"
                  placeholder="Enter amount"
                  value={newContribution.amount}
                  onChange={(e) => setNewContribution(prev => ({ ...prev, amount: e.target.value }))}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="type">Type</Label>
                <Select
                  value={newContribution.type}
                  onValueChange={(value: 'monthly' | 'additional') => 
                    setNewContribution(prev => ({ ...prev, type: value }))
                  }
                >
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="monthly">Monthly Contribution</SelectItem>
                    <SelectItem value="additional">Additional Savings</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div className="space-y-2">
                <Label htmlFor="description">Description (Optional)</Label>
                <Input
                  id="description"
                  placeholder="Enter description"
                  value={newContribution.description}
                  onChange={(e) => setNewContribution(prev => ({ ...prev, description: e.target.value }))}
                />
              </div>
              <div className="flex justify-end space-x-2">
                <Button variant="outline" onClick={() => setAddContributionOpen(false)}>
                  Cancel
                </Button>
                <Button onClick={handleAddContribution}>
                  Add Contribution
                </Button>
              </div>
            </div>
          </DialogContent>
        </Dialog>
      </div>

      {/* Summary Cards */}
      <div className="grid md:grid-cols-3 gap-6">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Contributions</CardTitle>
            <DollarSign className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{formatCurrency(totalContributions)}</div>
            <p className="text-xs text-muted-foreground">
              From {contributions.length} contributions
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Monthly Contributions</CardTitle>
            <Calendar className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {formatCurrency(monthlyContributions.reduce((sum, cont) => sum + cont.amount, 0))}
            </div>
            <p className="text-xs text-muted-foreground">
              {monthlyContributions.length} monthly contributions
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Additional Savings</CardTitle>
            <TrendingUp className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              {formatCurrency(additionalContributions.reduce((sum, cont) => sum + cont.amount, 0))}
            </div>
            <p className="text-xs text-muted-foreground">
              {additionalContributions.length} additional contributions
            </p>
          </CardContent>
        </Card>
      </div>

      {/* Contributions Table */}
      <Card>
        <CardHeader>
          <CardTitle>Contribution History</CardTitle>
          <CardDescription>
            All your contributions and savings history
          </CardDescription>
        </CardHeader>
        <CardContent>
          {contributions.length === 0 ? (
            <div className="text-center py-8">
              <DollarSign className="h-12 w-12 text-muted-foreground mx-auto mb-4" />
              <h3 className="text-lg font-medium mb-2">No contributions yet</h3>
              <p className="text-muted-foreground mb-4">
                Start building your savings by adding your first contribution
              </p>
              <Button onClick={() => setAddContributionOpen(true)}>
                <Plus className="w-4 h-4 mr-2" />
                Add First Contribution
              </Button>
            </div>
          ) : (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Date</TableHead>
                  <TableHead>Type</TableHead>
                  <TableHead>Description</TableHead>
                  <TableHead className="text-right">Amount</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {contributions
                  .sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
                  .map((contribution) => (
                    <TableRow key={contribution.id}>
                      <TableCell>{formatDate(contribution.date)}</TableCell>
                      <TableCell>
                        <Badge variant={contribution.type === 'monthly' ? 'default' : 'secondary'}>
                          {contribution.type === 'monthly' ? 'Monthly' : 'Additional'}
                        </Badge>
                      </TableCell>
                      <TableCell className="font-medium">{contribution.description}</TableCell>
                      <TableCell className="text-right font-medium">
                        {formatCurrency(contribution.amount)}
                      </TableCell>
                    </TableRow>
                  ))}
              </TableBody>
            </Table>
          )}
        </CardContent>
      </Card>
    </div>
  );
}