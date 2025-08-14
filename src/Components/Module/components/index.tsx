import { observer } from 'mobx-react-lite';
import { type FC, useEffect } from 'react';
import { ModulePriority } from '@/Components/Module/components/priority.ts';
import styles from './index.module.scss';
import { ModulePreset } from './preset.ts';
import { ModuleStorage } from './storage.ts';
import { ModuleStore } from './store.ts';
import type { SortedModuleProps } from './typings.ts';
export const Modules: FC = observer(() => {
  const { setSortedModules, availableModules } = ModuleStore;
  useEffect(() => {
    const storageItems = ModuleStorage.getItems();
    const sorted: SortedModuleProps[] = [];
    for (const preset of ModulePreset.items) {
      sorted.push({
        id: preset.id,
        priority:
          Number(storageItems?.[preset.id]) ||
          ModulePriority.indexOf(preset.id),
      });
    }
    setSortedModules(sorted);
  }, [setSortedModules]);
  if (!availableModules.length) {
    return null;
  }
  return (
    <div className={styles.container}>
      {availableModules.map(({ id, content: C }) => {
        return <C key={id} />;
      })}
    </div>
  );
});
