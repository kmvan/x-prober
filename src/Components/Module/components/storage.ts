import type { StoragePriorityItemProps } from './store.ts';

const STORAGE_KEY = 'module-priority';
export const ModuleStorage = {
  getItems(): Record<string, number> {
    const items = localStorage.getItem(STORAGE_KEY);
    if (!items) {
      return {};
    }
    try {
      return JSON.parse(items) as Record<string, number>;
    } catch {
      return {};
    }
  },
  setItems(items: Record<string, number>) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
  },
  getPriority(id: string): number {
    return this.getItems()[id] || 0;
  },
  setPriority({ id, priority }: StoragePriorityItemProps) {
    const items = this.getItems();
    items[id] = priority;
    this.setItems(items);
  },
};
