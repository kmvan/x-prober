import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { gettext } from '../../Language'
import { Portal } from '../../Utils/components/portal'
import { ToastStore } from '../stores'
import styles from './styles.module.scss'
export const Toast: FC = observer(() => {
  const { isOpen, msg, close } = ToastStore
  if (!isOpen) {
    return null
  }
  return (
    <Portal>
      <div
        className={styles.main}
        title={gettext('Click to close')}
        onClick={() => close()}
      >
        {msg}
      </div>
    </Portal>
  )
})
