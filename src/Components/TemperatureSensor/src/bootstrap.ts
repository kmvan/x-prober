import { CardStore } from '../../Card/src/stores'
import { gettext } from '../../Language/src'
import { TemperatureSensor as component } from './components'
import { TemperatureSensorConstants } from './constants'
export const TemperatureSensorBoostrap = (): void => {
  const { id } = TemperatureSensorConstants
  CardStore.addCard({
    id,
    title: gettext('Temperature Sensor'),
    tinyTitle: gettext('Temp.'),
    enabled: false,
    priority: 240,
    component,
  })
}
