import { configure, makeAutoObservable } from 'mobx';
import { ModulePriority } from '@/Components/Module/components/priority.ts';
import { PollStore } from '@/Components/Poll/components/store.ts';
import type { PollDataProps } from '@/Components/Poll/components/typings.ts';
import { ModulePreset } from './preset.ts';
import { ModuleStorage } from './storage.ts';
import type { ModuleProps, SortedModuleProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
export interface StoragePriorityItemProps {
  id: string;
  priority: number;
}
const saveSortedStorage = (items: SortedModuleProps[]) => {
  const sorted: Record<string, number> = {};
  for (const item of items) {
    sorted[item.id] = item.priority;
  }
  ModuleStorage.setItems(sorted);
};
class Main {
  sortedModules: SortedModuleProps[] = [];
  constructor() {
    makeAutoObservable(this);
  }
  setSortedModules = (modules: SortedModuleProps[]) => {
    this.sortedModules = modules.toSorted((a, b) => {
      return a.priority - b.priority;
    });
  };
  get availableModules(): ModuleProps[] {
    const { pollData } = PollStore;
    const items = ModulePreset.items
      .filter(({ id }) => Boolean(pollData?.[id as keyof PollDataProps]))
      .toSorted((a, b) => {
        const moduleA = this.sortedModules.find((item) => item.id === a.id);
        const moduleB = this.sortedModules.find((item) => item.id === b.id);
        return (
          Number(moduleA?.priority ?? ModulePriority.indexOf(a.id)) -
          Number(moduleB?.priority ?? ModulePriority.indexOf(b.id))
        );
      });
    return items;
  }
  moveUp = (id: string) => {
    const i = this.sortedModules.findIndex((item) => item.id === id);
    if (i === 0) {
      return;
    }
    const tmp = this.sortedModules[i].priority;
    this.sortedModules[i].priority = this.sortedModules[i - 1].priority;
    this.sortedModules[i - 1].priority = tmp;
    this.sortedModules.sort((a, b) => a.priority - b.priority);
    saveSortedStorage(this.sortedModules);
  };
  moveDown = (id: string) => {
    const i = this.sortedModules.findIndex((item) => item.id === id);
    if (i === this.sortedModules.length - 1) {
      return;
    }
    const tmp = this.sortedModules[i].priority;
    this.sortedModules[i].priority = this.sortedModules[i + 1].priority;
    this.sortedModules[i + 1].priority = tmp;
    this.sortedModules.sort((a, b) => a.priority - b.priority);
    saveSortedStorage(this.sortedModules);
  };
  get disabledMoveUpId(): string {
    const items = this.availableModules;
    if (items.length <= 1) {
      return '';
    }
    return items[0].id;
  }
  get disabledMoveDownId(): string {
    const items = this.availableModules;
    if (items.length <= 1) {
      return '';
    }
    return items.at(-1)?.id ?? '';
  }
}
export const ModuleStore = new Main();
