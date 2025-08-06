import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { CardGroup } from '@/Components/Card/components/group';
import { CardItem } from '@/Components/Card/components/item.tsx';
import { CardSingleColContainer } from '@/Components/Card/components/single-col-container.tsx';
import { gettext } from '@/Components/Language/index.ts';
import { Meter } from '@/Components/Meter/components';
import { template } from '@/Components/Utils/components/template';
import { TemperatureSensorConstants } from './constants.ts';
import { TemperatureSensorStore } from './store.ts';
export const TemperatureSensor: FC = observer(() => {
  const { itemsCount, items } = TemperatureSensorStore;
  if (!itemsCount) {
    return null;
  }
  return (
    <CardItem
      id={TemperatureSensorConstants.id}
      title={gettext('Templerature sensor')}
    >
      <CardSingleColContainer>
        {items.map(({ id, name, celsius }) => (
          <CardGroup
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
          </CardGroup>
        ))}
      </CardSingleColContainer>
    </CardItem>
  );
});
