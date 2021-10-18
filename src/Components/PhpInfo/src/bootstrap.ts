import { CardStore } from '../../Card/src/stores'
import { gettext } from '../../Language/src'
import { PhpInfo as component } from './components'
import { PhpInfoConstants } from './constants'

export const PhpInfoBootstrap = (): void => {
  const { id, isEnable } = PhpInfoConstants
  isEnable &&
    CardStore.addCard({
      id,
      title: gettext('PHP Information'),
      tinyTitle: gettext('PHP'),
      priority: 400,
      component,
    })
}
