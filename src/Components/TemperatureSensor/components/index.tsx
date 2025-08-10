import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { Meter } from '@/Components/Meter/components';
import { ModuleGroup } from '@/Components/Module/components/group.tsx';
import { ModuleItem } from '@/Components/Module/components/item.tsx';
import { template } from '@/Components/Utils/components/template';
import { UiSingleColContainer } from '@/Components/ui/col/single-container.tsx';
import { TemperatureSensorConstants } from './constants.ts';
import { TemperatureSensorStore } from './store.ts';
export const TemperatureSensor: FC = observer(() => {
  const { pollData } = TemperatureSensorStore;
  if (!pollData?.items?.length) {
    return null;
  }
  const { items } = pollData;
  return (
    <ModuleItem
      id={TemperatureSensorConstants.id}
      title={gettext('Templerature sensor')}
    >
      <UiSingleColContainer>
        {items.map(({ id, name, celsius }) => (
          <ModuleGroup
            key={id}
            title={template(gettext('{{sensor}} temperature'), {
              sensor: name,
            })}
          >
            <Meter
              isCapacity={false}
              max={150}
              percentTag="℃"
              value={celsius}
            />
          </ModuleGroup>
        ))}
      </UiSingleColContainer>
    </ModuleItem>
  );
});
