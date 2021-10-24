import { CardStore } from '../../Card/src/stores'
import { gettext } from '../../Language/src'
import { MyInfo as component } from './components'
import { MyInfoConstants } from './constants'
export const MyInfoBootstrap = (): void => {
  const { id, isEnable } = MyInfoConstants
  isEnable &&
    CardStore.addCard({
      id,
      title: gettext('My Information'),
      tinyTitle: gettext('Mine'),
      priority: 900,
      component,
    })
}
