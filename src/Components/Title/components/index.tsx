import { observer } from 'mobx-react-lite'
import { FC, HTMLProps } from 'react'
import { BootstrapConstants } from '../../Bootstrap/constants'
import { UpdaterLink } from '../../Updater/components/updater-link'
import { UpdaterStore } from '../../Updater/stores'
import styles from './styles.module.scss'
export const TitleLink: FC<HTMLProps<HTMLAnchorElement>> = (props) => (
  <a className={styles.link} {...props} />
)
export const Title: FC = observer(() => {
  const { appUrl, appName, version } = BootstrapConstants
  return (
    <h1 className={styles.h1}>
      {UpdaterStore.newVersion ? (
        <UpdaterLink />
      ) : (
        <TitleLink href={appUrl} target='_blank' rel='noreferrer'>
          {`${appName} v${version}`}
        </TitleLink>
      )}
    </h1>
  )
})
