import hexToRgb from '@/Helper/src/components/hex-to-rgb'
import { gettext } from '@/Language/src'

const schemes = {
  default: {
    name: gettext('X Prober'),
    colorDark: '#333333',
    colorDarkDeep: '#000',
    colorGray: '#f8f8f8',
    colorDownload: '#007400',
    colorUpload: '#c24b00',
    textShadowWithDarkBg: '0 1px 1px #333333',
    textShadowWithLightBg: '0 1px 1px #f8f8f8',
    colorDarkRgb: hexToRgb('#333333'),
  },
  azuki: {
    name: 'AZUKI',
    colorDark: '#954A45',
    colorDarkDeep: '#000',
    colorGray: '#f8f8f8',
    colorDownload: '#007400',
    colorUpload: '#c24b00',
    textShadowWithDarkBg: '0 1px 1px #954A45',
    textShadowWithLightBg: '0 1px 1px #f8f8f8',
    colorDarkRgb: hexToRgb('#954A45'),
  },
  kenpohzome: {
    name: 'KENPOHZOME',
    colorDark: '#43341B',
    colorDarkDeep: '#000',
    colorGray: '#f8f8f8',
    colorDownload: '#007400',
    colorUpload: '#c24b00',
    textShadowWithDarkBg: '0 1px 1px #43341B',
    textShadowWithLightBg: '0 1px 1px #f8f8f8',
    colorDarkRgb: hexToRgb('#43341B'),
  },
  innstudio: {
    name: 'INN STUDIO',
    colorDark: '#795548',
    colorDarkDeep: '#000',
    colorGray: '#f8f8f8',
    colorDownload: '#007400',
    colorUpload: '#c24b00',
    textShadowWithDarkBg: '0 1px 1px #795548',
    textShadowWithLightBg: '0 1px 1px #f8f8f8',
    colorDarkRgb: hexToRgb('#795548'),
  },
  aisumicha: {
    name: 'AISUMICHA',
    colorDark: '#373C38',
    colorDarkDeep: '#000',
    colorGray: '#f8f8f8',
    colorDownload: '#007400',
    colorUpload: '#c24b00',
    textShadowWithDarkBg: '0 1px 1px #373C38',
    textShadowWithLightBg: '0 1px 1px #f8f8f8',
    colorDarkRgb: hexToRgb('#373C38'),
  },
}

export default schemes
