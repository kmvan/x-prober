import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { BootstrapConstants } from '../../Bootstrap/constants'
import { gettext } from '../../Language'
import { formatBytes } from '../../Utils/components/format-bytes'
import { template } from '../../Utils/components/template'
import { FooterStore } from '../stores'
import styles from './styles.module.scss'
export const Footer: FC = observer(() => {
  const { appName, appUrl, authorName, authorUrl } = BootstrapConstants
  const { memUsage, time } = FooterStore.conf
  return (
    <div
      className={styles.main}
      dangerouslySetInnerHTML={{
        __html: template(
          gettext(
            'Generator {{appName}} / Author {{authorName}} / {{memUsage}} / {{time}}ms',
          ),
          {
            appName: `<a href="${appUrl}" target="_blank">${appName}</a>`,
            authorName: `<a href="${authorUrl}" target="_blank">${authorName}</a>`,
            memUsage: formatBytes(memUsage),
            time: (time * 1000).toFixed(2),
          },
        ),
      }}
    />
  )
})
