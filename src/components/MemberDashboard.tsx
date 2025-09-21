import React, { useState } from 'react';
import { AppState } from '../App';
import { Sidebar, SidebarContent, SidebarHeader, SidebarMenu, SidebarMenuItem, SidebarMenuButton, SidebarProvider, SidebarTrigger } from './ui/sidebar';
import { Button } from './ui/button';
import { User, DollarSign, CreditCard, LogOut, Menu } from 'lucide-react';
import { ProfileView } from './member/ProfileView';
import { ContributionView } from './member/ContributionView';
import { LoanView } from './member/LoanView';

interface MemberDashboardProps {
  state: AppState;
  updateState: (updates: Partial<AppState>) => void;
  onLogout: () => void;
}

export function MemberDashboard({ state, updateState, onLogout }: MemberDashboardProps) {
  const [activeTab, setActiveTab] = useState('profile');
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const currentMember = state.currentUser!;
  const memberLoans = state.loans.filter(loan => loan.memberId === currentMember.memberId);
  const memberContributions = state.contributions.filter(cont => cont.memberId === currentMember.memberId);

  const menuItems = [
    { id: 'profile', label: 'Profile', icon: User },
    { id: 'contributions', label: 'Contributions', icon: DollarSign },
    { id: 'loans', label: 'Loans', icon: CreditCard },
  ];

  const renderContent = () => {
    switch (activeTab) {
      case 'profile':
        return <ProfileView member={currentMember} state={state} updateState={updateState} />;
      case 'contributions':
        return <ContributionView 
          member={currentMember} 
          contributions={memberContributions}
          state={state}
          updateState={updateState}
        />;
      case 'loans':
        return <LoanView 
          member={currentMember}
          loans={memberLoans}
          state={state}
          updateState={updateState}
        />;
      default:
        return <ProfileView member={currentMember} state={state} updateState={updateState} />;
    }
  };

  return (
    <SidebarProvider>
      <div className="flex min-h-screen w-full">
        <Sidebar className="border-r">
          <SidebarHeader className="p-4 border-b">
            <div className="flex items-center space-x-2">
              <div className="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                <User className="w-4 h-4 text-primary-foreground" />
              </div>
              <div>
                <p className="font-medium">{currentMember.name}</p>
                <p className="text-sm text-muted-foreground">{currentMember.memberId}</p>
              </div>
            </div>
          </SidebarHeader>
          
          <SidebarContent className="p-4">
            <SidebarMenu>
              {menuItems.map((item) => (
                <SidebarMenuItem key={item.id}>
                  <SidebarMenuButton
                    onClick={() => setActiveTab(item.id)}
                    isActive={activeTab === item.id}
                    className="w-full"
                  >
                    <item.icon className="w-4 h-4" />
                    <span>{item.label}</span>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              ))}
            </SidebarMenu>
            
            <div className="mt-auto pt-4 border-t">
              <Button
                variant="ghost"
                onClick={onLogout}
                className="w-full justify-start"
              >
                <LogOut className="w-4 h-4 mr-2" />
                Logout
              </Button>
            </div>
          </SidebarContent>
        </Sidebar>

        <div className="flex-1 flex flex-col">
          <header className="border-b bg-background p-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center space-x-4">
                <SidebarTrigger className="lg:hidden">
                  <Menu className="w-5 h-5" />
                </SidebarTrigger>
                <h1 className="text-xl font-semibold">
                  {menuItems.find(item => item.id === activeTab)?.label} Dashboard
                </h1>
              </div>
              <div className="text-sm text-muted-foreground">
                Welcome back, {currentMember.name}
              </div>
            </div>
          </header>

          <main className="flex-1 p-6">
            {renderContent()}
          </main>
        </div>
      </div>
    </SidebarProvider>
  );
}