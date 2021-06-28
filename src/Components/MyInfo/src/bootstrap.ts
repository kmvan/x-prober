import { CardStore } from '../../Card/src/stores'
import { gettext } from '../../Language/src'
import { MyInfo as component } from './components'
import { MyInfoStore } from './stores'
export const MyInfoBootstrap = (): void => {
  MyInfoStore.enabled &&
    CardStore.addCard({
      id: MyInfoStore.ID,
      title: gettext('My Information'),
      tinyTitle: gettext('Mine'),
      priority: 900,
      component,
    })
}
