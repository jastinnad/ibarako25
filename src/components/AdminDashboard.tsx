import React, { useState } from 'react';
import { AppState } from '../App';
import { Sidebar, SidebarContent, SidebarHeader, SidebarMenu, SidebarMenuItem, SidebarMenuButton, SidebarProvider, SidebarTrigger } from './ui/sidebar';
import { Button } from './ui/button';
import { Badge } from './ui/badge';
import { Users, CreditCard, Bell, Settings, LogOut, Menu, Shield } from 'lucide-react';
import { MemberManagement } from './admin/MemberManagement';
import { LoanManagement } from './admin/LoanManagement';
import { NotificationCenter } from './admin/NotificationCenter';
import { SystemSettings } from './admin/SystemSettings';

interface AdminDashboardProps {
  state: AppState;
  updateState: (updates: Partial<AppState>) => void;
  onLogout: () => void;
}

export function AdminDashboard({ state, updateState, onLogout }: AdminDashboardProps) {
  const [activeTab, setActiveTab] = useState('members');

  const pendingNotifications = state.notifications.filter(n => n.status === 'pending').length;
  const pendingLoans = state.loans.filter(l => l.status === 'pending').length;

  const menuItems = [
    { 
      id: 'members', 
      label: 'Members', 
      icon: Users, 
      description: `${state.users.filter(u => u.role === 'member').length} members` 
    },
    { 
      id: 'loans', 
      label: 'Loans', 
      icon: CreditCard, 
      description: `${pendingLoans} pending`,
      badge: pendingLoans > 0 ? pendingLoans : undefined
    },
    { 
      id: 'notifications', 
      label: 'Notifications', 
      icon: Bell, 
      description: `${pendingNotifications} pending`,
      badge: pendingNotifications > 0 ? pendingNotifications : undefined
    },
    { 
      id: 'settings', 
      label: 'Settings', 
      icon: Settings, 
      description: 'System configuration' 
    },
  ];

  const renderContent = () => {
    switch (activeTab) {
      case 'members':
        return <MemberManagement state={state} updateState={updateState} />;
      case 'loans':
        return <LoanManagement state={state} updateState={updateState} />;
      case 'notifications':
        return <NotificationCenter state={state} updateState={updateState} />;
      case 'settings':
        return <SystemSettings state={state} updateState={updateState} />;
      default:
        return <MemberManagement state={state} updateState={updateState} />;
    }
  };

  return (
    <SidebarProvider>
      <div className="flex min-h-screen w-full">
        <Sidebar className="border-r">
          <SidebarHeader className="p-4 border-b">
            <div className="flex items-center space-x-2">
              <div className="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                <Shield className="w-4 h-4 text-primary-foreground" />
              </div>
              <div>
                <p className="font-medium">Admin Panel</p>
                <p className="text-sm text-muted-foreground">{state.currentUser?.name}</p>
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
                    <div className="flex items-center justify-between w-full">
                      <div className="flex items-center space-x-2">
                        <item.icon className="w-4 h-4" />
                        <div className="flex flex-col items-start">
                          <span>{item.label}</span>
                          <span className="text-xs text-muted-foreground">{item.description}</span>
                        </div>
                      </div>
                      {item.badge && (
                        <Badge variant="destructive" className="ml-auto">
                          {item.badge}
                        </Badge>
                      )}
                    </div>
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
                  {menuItems.find(item => item.id === activeTab)?.label} Management
                </h1>
              </div>
              <div className="flex items-center space-x-4">
                {pendingNotifications > 0 && (
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setActiveTab('notifications')}
                    className="relative"
                  >
                    <Bell className="w-4 h-4" />
                    {pendingNotifications > 0 && (
                      <Badge 
                        variant="destructive" 
                        className="absolute -top-2 -right-2 h-5 w-5 flex items-center justify-center p-0 text-xs"
                      >
                        {pendingNotifications}
                      </Badge>
                    )}
                  </Button>
                )}
                <div className="text-sm text-muted-foreground">
                  Administrator Dashboard
                </div>
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